<?php

function get_db_size ()
{
	$db = mysqli_connect( DB_HOST, DB_USER, DB_PASSWORD, 'information_schema' );

	$db_size_sql = "SELECT TABLE_NAME, DATA_LENGTH, INDEX_LENGTH FROM information_schema.TABLES WHERE `TABLE_SCHEMA` LIKE '".DB_NAME."'";
	$db_size_query = mysqli_query( $db, $db_size_sql );

	$db_size_sum = 0;
	for ( $i = 0; $i < mysqli_num_rows($db_size_query); $i++ )
	{
		$row = mysqli_fetch_assoc( $db_size_query );
		$db_table[$row['TABLE_NAME']] = ($row['DATA_LENGTH'] + $row['INDEX_LENGTH']) / 1024 / 1024;
		$db_size_sum = $db_size_sum + ($row['DATA_LENGTH'] + $row['INDEX_LENGTH']);
	}

	return array("db_size" => $db_size_sum / 1024 / 1024, "db_tables_size" => $db_table);
}

