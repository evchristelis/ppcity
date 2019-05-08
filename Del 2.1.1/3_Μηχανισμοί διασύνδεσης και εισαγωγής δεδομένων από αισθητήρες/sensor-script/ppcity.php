<?php

define("DATABASE_HOST", "localhost");
define("DATABASE_PORT", "5432");
define("DATABASE_NAME", "ppcity");
define("DATABASE_USER", "ppcity");
define("DATABASE_PASS", "KymLxM8egydmQLY");
define("DATABASE_SCHEMA", "sensordata");

function schema($table)
{
	$map = array(
		'sensors' => DATABASE_SCHEMA,
	);

	return isset($map[$table]) ? $map[$table].".".$table : $table;
}

include_once __DIR__."/lib/script.php";
include_once __DIR__."/lib/php.php";
include_once __DIR__."/lib/db.php";
include_once __DIR__."/lib/curl.php";

function sensor_table_columns()
{
    return array(
        'id' => 'numeric',
        'status' => 'boolean',
        'label' => 'string',
        'name' => 'string',
        'location' => 'geom',
        'encloser_number' => 'string',
        'type_id' => 'numeric',
        'type_name' => 'string',
        'organization_id' => 'numeric',
        'organization_name' => 'string',

        'longitude' => 'numeric',
        'latitude' => 'numeric',
        'data_json' => 'json',
    );
}

function add_sensor($data)
{
	// logit(jsonit($data));

	$location = $data['location'];

	if (trim($location)==='')
	{
		$data['location'] = null;
		$data['longitude'] = null;
		$data['latitude'] = null;
	}
	else
	{
		list($latitude, $longitude) = explode(",", $location);

		$data['location'] = 'st_setSRID(st_MakePoint('.$longitude.','.$latitude.'), 4326)';
		$data['longitude'] = $longitude;
		$data['latitude'] = $latitude;
	}

	$data['status'] = $data['status'][0]==='T' ? 't' : 'f';

	$current = fetch_if_in_table("sensors", "id='".$data['id']."'", "*");

	if ($current!==false)
	{
		foreach ($data as $key => $value)
		{
			if ($value!==$current[$key] and $key!=='location')
			{
				logit("diff in ".$key." : ".$value." != ".$current[$key]);

				$sql = update_sql("sensors", $data, $data['id'], 'id');

				$result = query($sql);

				if (!$result)
				{
					logit("Failed to run ".$sql, "ERROR");
					return false;
				}

				break;
			}
		}
	}
	else
	{
		$data['data_json'] = null;

		$sql = insert_sql("sensors", $data);

		$result = query($sql);

		if (!$result)
		{
			logit("Failed to run ".$sql, "ERROR");
			return false;
		}
	}

	return fetch_if_in_table("sensors", "id='".$data['id']."'", "id, label, status, *");
}

//
// run
//

// echo array_to_postgres_table(sensor_table_columns(), "sensors", DATABASE_SCHEMA);

$url = "http://app.ppcity.eu/api/api.php";

$ch = post_channel($url."?func=getSensors");

$output = curl_exec($ch);

if (http_code_is($ch, 200))
{
	$a = json_decode($output, true);

	db_connect("die");

	$updated = 0;

	foreach($a as $row)
	{
		$sensor = add_sensor($row);

		if ($sensor!==false and $sensor['status']==='t')
		{
			$data_url = $url."?func=getLastRecord&type=".$sensor['type_id']."&device_id=".$sensor['id'];

			$ch = post_channel($data_url);

			$output = curl_exec($ch);

			$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

			if ($http_code==200)
			{
				// logit($data_url."\n".$output);

				$json = json_decode($output, true);

				if ($json===false)
				{
					logit("invalid json from ".$data_url, "WARN");
				}
				elseif ($sensor['data_json']===$output)
				{
					// skip update, cause nothing changed
					logit("Already saved ".$data_url, "DEBUG");
				}
				elseif (isset($json[0]['error']))
				{
					logit("remote error: ".$data_url."\n\t".$json[0]['error'], "DEBUG");
				}
				else
				{
					$sql = update_sql("sensors", array(
						"data_json" => $output,
						"modified" => "now()",
					), $sensor['id'], 'id');

					$result = query($sql);

					if (!$result)
						logit("Failed to update data_json for ".$sensor['id']." - ".$sensor['label']);
					else
						$updated++;

				}
			}
			else
			{
				logit("code ".$http_code." from ".$data_url, "WARN");
			}
		}
	}

	logit("Updated ".$updated."/".count($a)." sensors", "INFO");
}
else
{
	logit("failed to get from ".$url);
}

curl_close($ch);

script_done();