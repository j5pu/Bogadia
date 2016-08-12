<?php
require_once('../../../wp-load.php');
$filter = $_POST['filter'];
$search = $_POST['search'];
$exclude = $_POST['exclude'];
$offset = $_POST['offset'];
$bogacontest->setSlug($_POST['slug']);
$bogacontest->setId($_POST['contest_id']);
$bogacontest->get_contestants($filter, '', $search, $exclude, $offset);
$bogacontest->print_contestants();