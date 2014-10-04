<?php

/**
 * Skills table:
 * id - int(3) NOT NULL
 * attack int(3) NOT NULL 
 * hp int(3) NOT NULL
 * crit int(3) NOT NULL
 * special (text) NOT NULL
 */
 
 
class Skills 
{
    private $dbh;

    public function __construct($database)
    {
        $this->dbh = $database;
    }
    
    public function skillsByClass($sclass)
    {
        //for now we will not directly choose what to select, until i'm more comftorable with this.
        $query = $this->dbh->prepare('SELECT * FROM `skills`
            WHERE `sclass` = ?
            AND `level` = 1
            ORDER BY `lvlreq`');
        $query->execute(array($sclass));
        return ($query->rowCount() > 0) ? $query->fetchAll() : false;
    }
    
    public function castSkillCheck($userid, $skillid)
    {
        $query = $this->dbh->prepare('SELECT `skillid` FROM `castskills`
            WHERE `playerid` = ?
            AND `skillid` = ?
            AND `duration` != 0');
        $query->execute(array($userid, $skillid));
        return ($query->rowCount() > 0) ? $query->fetch : false;
    }
    
    public function insertCastSkill($userid, $skillid, $skill_level, $duration, $recharge)
    {
        $query = $this->dbh->prepare('INSERT INTO `castskills`
                (`playerid`,`casterid`,`skillid`,`level`,`duration`,`recharge`)
            VALUES
                (?,?,?,?,?,?)');
        $query->execute(array($userid, $userid, $skillid, $skill_level, $duration, $recharge));
    }
    
    public function updatePlayerSkillCharge($userid, $skillid)
    {
        $query = $this->dbh->prepare('UPDATE `stats` SET `charged` = 0
            WHERE `playerid` = ?
            AND `skillid` = ?');
        $query->execute(array($userid, $skillid));
    }
    

}

//Casting the skills. This page gets loaded through ajax.

$skills = new Skills($dbh);

if (isset($_GET['skillid']))
{
    $userid = Session::get('userid');
    $skillid = htmlentities((int)$_GET['skillid']);
    if ($skills->playerSkillLevel($skillid, $userid) == false)
    {
        echo 'You may not cast this skill; you have not learned it.';
        exit;
    }
    else
    {
        $skill_level = $skills->playerSkillLevel($skillid, $userid);
    }
    foreach ($skills->fetchSkillDataDesc($skillid) as $skillDataDesc)
    {
        $name = $skillDataDesc['name'];
        $duration = $skillDataDesc['duration'];
        $recharge = $skillDataDesc['recharge'];
        $special = $skillDataDesc['special'];
        $max_level = $skillDataDesc['level'];
        $skill_level = ($skill_level > $max_level) ? $max_level : $skill_level;
    }
    if ($skills->skillDescData($skillid, $skill_level) == false)
    {
        echo 'This skill does not exist.';
        exit;
    }
    if ($skills->castSkillCheck($userid, $skillid) == false)
    {
        if ($skills->castRecharge($userid, $skillid) != false)
        {
            echo 'This skill is recharging.';
            exit;
        }
        
        //insert the casted skill.
        $skills->insertCastSkill($userid, $skillid, $skill_level, $duration, $recharge);
        $skills->updatePlayerSkillCharge($userid, $skillid);
        
        /**
         * this is dangerous with the eval. Maybe creating a table devoted
         * to holding all 'special' info would be more logical. The eval seems
         * like a good idea, but its lazy and just an unneccessary security risk!
         */
        if ($special != '')
        {
            eval($special);
        }
        require_once '../updatestats.php';
        echo $name.' has been casted!';
    }
    else
    {
        echo 'This skill is already casted.';
        exit;
    }
}
    



