<?php
require_once('../../../wp-load.php');
$filter = $_POST['filter'];
$search = $_POST['search'];
$bogacontest->setSlug($_POST['slug']);
$bogacontest->get_contestants($filter, '', $search);
$bogacontest->print_contestants();