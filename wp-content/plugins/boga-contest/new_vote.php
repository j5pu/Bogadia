<?php
require_once('../../../wp-load.php');
$bogacontestant->setID($_POST['contestant_id']);
echo $bogacontestant->anotate_vote($_POST['voter_id']);