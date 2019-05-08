<?php

//
// database
//

function db_connect($error_function="die")
{
    $index = "POSTGRES_CONNECTION";

    if (!isset($GLOBALS[$index]))
    {
        $db = pg_connect("host=".DATABASE_HOST." port=".DATABASE_PORT." dbname=".DATABASE_NAME." user=".DATABASE_USER." password=".DATABASE_PASS) or $error_function('Could not connect: ' . pg_last_error());

        if (!$db)
            die("DATABASE CONNECT FAILED");

        $GLOBALS[$index] = $db;
    }

    return $GLOBALS[$index];
}

function query($sql)
{
    $results = pg_query($sql);

    return $results;
}

function transaction($sqls)
{
    return query("BEGIN;".implode(";\n\r", $sqls)."COMMIT;");
}

function num_rows($result)
{
    return pg_num_rows($result);
}

function fetch_assoc($result)
{
    return pg_fetch_assoc($result);
}

function sql_case($column, $a, $default=false)
{
    if ($default===null)
        $default = 'NULL';
    elseif ($default===false)
        $default = "'".key($a)."'";

    $sql = "CASE ";
    foreach ($a as $key => $value)
        $sql .= "WHEN $column::text=$value::text THEN '$key' ";

    return $sql."ELSE $default END";
}

function if_null_sql($column, $default='-')
{
    return "CASE WHEN $column IS NULL THEN '$default' ELSE $column END";
}

function if_not_null_sql($column, $value)
{
    return "CASE WHEN $column IS NULL THEN NULL ELSE $value END";
}

function asc_order($columns)
{
    return "ORDER BY $columns ASC";
}

function desc_order($columns)
{
    return "ORDER BY $columns DESC";
}

function simple_table_join($table, $column1, $column2, $join_type="LEFT")
{
    return " $join_type JOIN $table ON ($column1=$column2)";
}

function table_join($table1, $table2, $table1_column, $table2_column, $as=null, $join_type="LEFT")
{
    $table1 = schema($table1);
    $table2 = schema($table2);

    if ($as)
        return simple_table_join("$table2 AS $as", "$table1.$table1_column", "$as.$table2_column", $join_type);
    else
        return simple_table_join($table2, "$table1.$table1_column", "$table2.$table2_column", $join_type);
}

function in_table($table, $column, $value, $where=null)
{
    db_connect();

    $table = schema($table);

    $where = ($where) ? " AND ($where)" : "";

    $sql = "SELECT $column FROM $table WHERE $column='$value' $where;";

    $result = query($sql);

    return ($result and num_rows($result)>0) ? true : false;
}

function fetch_if_in_table($table, $where, $columns="*")
{
    db_connect();

    $table = schema($table);

    $sql = "SELECT $columns FROM $table WHERE $where";

    $result = query($sql);

    return ($result and num_rows($result)==1) ? fetch_assoc($result) : false;
}

function prepare_value($value)
{
    if ($value===null)
        return 'null';

    if (starts_with($value, 'st_setSRID(') and ends_with($value, ")"))
        return $value;

    if (ends_with($value, '()') or (ends_with(trim($value), ')') and starts_with(trim($value),'(SELECT')))
        return $value;

    return "'".$value."'";
}

function prepare_values($values)
{
    foreach ($values as $key => $value)
        $values[$key] = prepare_value($value);

    return $values;
}

function insert_sql($table, $values)
{
    $values = prepare_values($values);

    return "INSERT INTO ".schema($table)." (".implode(",", array_keys($values)).", created) VALUES (".implode(",", $values).",now());";
}

function with_insert_sql($table, $values, $as, $return='id')
{
    return "WITH $as AS (".rtrim(insert_sql($table, $values,''),';')." RETURNING $return)";
}

function update_sql($table, $values, $id, $primary_key='id', $where=false)
{
    $updates = array();

    foreach ($values as $key => $value)
        $updates[$key] = $key.'='.prepare_value($value);

    $where = ($where) ? " AND ".$where : "";

    $sql = "UPDATE ".schema($table)." SET ".implode(',', $updates)." WHERE ".$primary_key.'='.prepare_value($id).$where.';'; //$primary_key='$id';";

    return $sql;
}

function delete_sql($table, $id, $primary_key="id", $semicolon=';')
{
    return "DELETE FROM ".schema($table)." WHERE $primary_key='".$id."'".$semicolon;
}

function simple_select_sql($table, $where="1=1", $column='id', $semicolon=true)
{
    $sql = "SELECT ".$column." FROM ".$table.' WHERE '.$where;

    return ($semicolon and $semicolon!='') ? $sql.';' : '('.$sql.')';
}

function select_sql($table, $where="1=1", $column='id', $semicolon=true)
{
    return simple_select_sql(schema($table), $where, $column, $semicolon);
}

function dump($in)
{
    echo "<pre>".json_encode($in, JSON_PRETTY_PRINT)."</pre><hr>";
}