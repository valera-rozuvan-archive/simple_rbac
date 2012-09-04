<?php
/*
 * Author    Valera Rozuvan
 * Created:  Sat Aug 25 17:09:03 EEST 2012
 * 
 * File:      Simple_rbacModule.php
 * Full path: protected/modules/simple_rbac/Simple_rbacModule.php
 *
 * Description: The 'father' model of the Simpe RBAC module. Will provide methods and properties that are accessible
 * from all of the module's controllers, views, and models.
 */

class Simple_rbacModule extends CWebModule
{
    public $setup = 0;

    public function install()
    {
        if (($this->setupMode() === true) && ($this->installed() === false)) {
            $sql = "
                CREATE TABLE ".SimpleRbacUsersDbTable::tableName()."
                (
                    `id`          INT (4) NOT NULL AUTO_INCREMENT,
                    `username`    VARCHAR (16) NOT NULL,
                    `password`    VARCHAR (128) NOT NULL,
                    `status`      TINYINT (1) NOT NULL DEFAULT '1' COMMENT 'Whether the user account is asctive and the user can log in. 1 - active, 0 - inactive.',
                    `last_access` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'The date and time when the user last logged in.',
                    `registered`  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'The date and time when this user was registered in the system.',
                    PRIMARY KEY (`id`)
                )
                ENGINE = InnoDB
                DEFAULT CHARSET = utf8
                COLLATE utf8_general_ci;

                CREATE TABLE ".SimpleRbacUsersInfoDbTable::tableName()."
                (
                   `users_id`   INT (4) NOT NULL,
                   `first_name` VARCHAR (20) NOT NULL,
                   `last_name`  VARCHAR (20) NOT NULL,
                   `email`      VARCHAR (30) NOT NULL,
                   PRIMARY KEY (`users_id`),
                   CONSTRAINT `fk_users_id` FOREIGN KEY (`users_id`) REFERENCES ".SimpleRbacUsersDbTable::tableName()." (`id`) ON DELETE CASCADE ON UPDATE CASCADE
                )
                ENGINE = InnoDB
                DEFAULT CHARSET = utf8
                COLLATE utf8_general_ci;

                create table {{AuthItem}}
                (
                   `name`                 varchar(64) not null,
                   `type`                 integer not null,
                   `description`          text,
                   `bizrule`              text,
                   `data`                 text,
                   primary key (`name`)
                ) engine InnoDB;

                create table {{AuthItemChild}}
                (
                   `parent`               varchar(64) not null,
                   `child`                varchar(64) not null,
                   primary key (`parent`,`child`),
                   foreign key (`parent`) references {{AuthItem}} (`name`) on delete cascade on update cascade,
                   foreign key (`child`) references {{AuthItem}} (`name`) on delete cascade on update cascade
                ) engine InnoDB;

                create table {{AuthAssignment}}
                (
                   `itemname`             varchar(64) not null,
                   `userid`               varchar(64) not null,
                   `bizrule`              text,
                   `data`                 text,
                   primary key (`itemname`,`userid`),
                   foreign key (`itemname`) references {{AuthItem}} (`name`) on delete cascade on update cascade
                ) engine InnoDB;
            ";
            $command = Yii::app()->db->createCommand($sql);
            $command->execute();
            $command->getPdoStatement()->closeCursor();
        }

        $this->createDefaultRoles();

        SRUser::createRole('admin');
        SRUser::createUser('admin', '1234', array('admin',));

        return $this->setupTableStatus();
    }

    public function uninstall()
    {
        if ($this->setupMode() === true) {
            $tables = $this->getModuleDbTables();

            foreach ($tables as $tableName) {
                if ($this->tableExists($tableName)) {
                    $sql = "
                        SET FOREIGN_KEY_CHECKS = 0;
                        DROP TABLE IF EXISTS ".$tableName.";
                        SET FOREIGN_KEY_CHECKS = 1;
                    ";

                    $command = Yii::app()->db->createCommand($sql);
                    $command->execute();
                    $command->getPdoStatement()->closeCursor();
                }
            }
        }

        return $this->setupTableStatus();
    }

    /*
     * This method will return
     *     true
     * when at least one table exists.
     */
    public function installed()
    {
        $returnStatus = false;

        $tables = $this->getModuleDbTables();
        foreach ($tables as $tableName) {
            if ($this->tableExists($tableName) === true) {
                $returnStatus = true;
                break;
            }
        }

        return $returnStatus;
    }

    public function setupTableStatus()
    {
        $tableStatus = array();
        $tables = $this->getModuleDbTables();
        foreach ($tables as $tableName)
            $tableStatus[$tableName] = $this->tableExists($tableName);

        return $tableStatus;
    }

    public function setupMode()
    {
        if (($this->setup !== 1) &&
            ($this->setup !== true) &&
            ($this->setup !== '1'))
            return false;

        return true;
    }

    public function tableExists($tableName)
    {
        // This needs to be called because CDbSchema caches everything, and if we drop or create a table in between
        // calls to this function, we have to refresh the cache to get current information about the table
        Yii::app()->getDb()->getSchema()->refresh();

        if (Yii::app()->getDb()->getSchema()->getTable($tableName) === null)
            return false;

        return true;
    }

    public function getModuleDbTables()
    {
        return array(
            '{{AuthItem}}',
            SimpleRbacUsersInfoDbTable::tableName(),
            SimpleRbacUsersDbTable::tableName(),
            '{{AuthItemChild}}',
            '{{AuthAssignment}}',
        );
    }

    private function createDefaultRoles()
    {
        $auth = Yii::app()->authManager;

        $bizRule = 'return Yii::app()->user->isGuest;';
        $auth->createRole('guest', 'guest user', $bizRule);

        $bizRule = 'return !Yii::app()->user->isGuest;';
        $auth->createRole('authenticated', 'authenticated user', $bizRule);

        $auth->save();
    }
}
