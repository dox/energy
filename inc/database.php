<?php
class db {
  protected $connection;
	protected $query;
  protected $show_errors = TRUE;
  protected $query_closed = TRUE;
	public $query_count = 0;

	public function __construct($dbhost = 'localhost', $dbuser = '', $dbpass = '', $dbname = '', $charset = 'utf8') {
		$this->connection = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
		if ($this->connection->connect_error) {
			$this->error('Failed to connect to MySQL - ' . $this->connection->connect_error);
		}
		$this->connection->set_charset($charset);
	}

  public function query($query) {
        if (!$this->query_closed) {
            $this->query->close();
        }
		if ($this->query = $this->connection->prepare($query)) {
            if (func_num_args() > 1) {
                $x = func_get_args();
                $args = array_slice($x, 1);
				$types = '';
                $args_ref = array();
                foreach ($args as $k => &$arg) {
					if (is_array($args[$k])) {
						foreach ($args[$k] as $j => &$a) {
							$types .= $this->_gettype($args[$k][$j]);
							$args_ref[] = &$a;
						}
					} else {
	                	$types .= $this->_gettype($args[$k]);
	                    $args_ref[] = &$arg;
					}
                }
				array_unshift($args_ref, $types);
                call_user_func_array(array($this->query, 'bind_param'), $args_ref);
            }
            $this->query->execute();
           	if ($this->query->errno) {
				$this->error('Unable to process MySQL query (check your params) - ' . $this->query->error);
           	}
            $this->query_closed = FALSE;
			$this->query_count++;
        } else {
            $this->error('Unable to prepare MySQL statement (check your syntax) - ' . $this->connection->error);
        }
		return $this;
  }

  public function insert($table, $data, $allowedColumns = null) {
    $data = $this->filterData($data, $allowedColumns);

    if (empty($data)) {
      $this->error('Unable to build INSERT query with no allowed columns');
    }

    $columns = array_keys($data);
    $placeholders = array_fill(0, count($columns), '?');
    $sql  = "INSERT INTO " . $this->identifier($table);
    $sql .= " (" . implode(", ", array_map(array($this, 'identifier'), $columns)) . ")";
    $sql .= " VALUES (" . implode(", ", $placeholders) . ")";

    return $this->query($sql, array_values($data));
  }

  public function update($table, $data, $whereColumn, $whereValue, $allowedColumns = null, $limit = 1) {
    $data = $this->filterData($data, $allowedColumns);

    if (empty($data)) {
      $this->error('Unable to build UPDATE query with no allowed columns');
    }

    $assignments = array();
    foreach (array_keys($data) as $column) {
      $assignments[] = $this->identifier($column) . " = ?";
    }

    $sql  = "UPDATE " . $this->identifier($table);
    $sql .= " SET " . implode(", ", $assignments);
    $sql .= " WHERE " . $this->identifier($whereColumn) . " = ?";

    $params = array_values($data);
    $params[] = $whereValue;

    if ($limit !== null) {
      $sql .= " LIMIT " . max(1, (int) $limit);
    }

    return $this->query($sql, $params);
  }

  public function delete($table, $whereColumn, $whereValue, $limit = 1) {
    $sql  = "DELETE FROM " . $this->identifier($table);
    $sql .= " WHERE " . $this->identifier($whereColumn) . " = ?";

    if ($limit !== null) {
      $sql .= " LIMIT " . max(1, (int) $limit);
    }

    return $this->query($sql, $whereValue);
  }

  public function multiQuery($query) {
    if (!$this->query_closed && $this->query) {
      $this->query->close();
      $this->query_closed = TRUE;
    }

    if (!$this->connection->multi_query($query)) {
      $this->error('Unable to process MySQL multi query - ' . $this->connection->error);
    }

    do {
      if ($result = $this->connection->store_result()) {
        $result->free();
      }
    } while ($this->connection->more_results() && $this->connection->next_result());

    if ($this->connection->error) {
      $this->error('Unable to process MySQL multi query - ' . $this->connection->error);
    }

    return $this;
  }


	public function fetchAll($callback = null) {
	    $params = array();
        $row = array();
	    $meta = $this->query->result_metadata();
	    while ($field = $meta->fetch_field()) {
	        $params[] = &$row[$field->name];
	    }
	    call_user_func_array(array($this->query, 'bind_result'), $params);
        $result = array();
        while ($this->query->fetch()) {
            $r = array();
            foreach ($row as $key => $val) {
                $r[$key] = $val;
            }
            if ($callback != null && is_callable($callback)) {
                $value = call_user_func($callback, $r);
                if ($value == 'break') break;
            } else {
                $result[] = $r;
            }
        }
        $this->query->close();
        $this->query_closed = TRUE;
		return $result;
	}

	public function fetchArray() {
	    $params = array();
        $row = array();
	    $meta = $this->query->result_metadata();
	    while ($field = $meta->fetch_field()) {
	        $params[] = &$row[$field->name];
	    }
	    call_user_func_array(array($this->query, 'bind_result'), $params);
        $result = array();
		while ($this->query->fetch()) {
			foreach ($row as $key => $val) {
				$result[$key] = $val;
			}
		}
        $this->query->close();
        $this->query_closed = TRUE;
		return $result;
	}

	public function close() {
		return $this->connection->close();
	}

    public function numRows() {
		$this->query->store_result();
		return $this->query->num_rows;
	}

	public function affectedRows() {
		return $this->query->affected_rows;
	}

	public function lastInsertID() {
    	return $this->connection->insert_id;
    }

    public function error($error) {
        if ($this->show_errors) {
            exit($error);
        }
    }

	private function _gettype($var) {
	    if (is_null($var)) return 's';
	    if (is_string($var)) return 's';
	    if (is_float($var)) return 'd';
	    if (is_int($var)) return 'i';
	    return 'b';
	}

  private function filterData($data, $allowedColumns = null) {
    if (!is_array($data)) {
      return array();
    }

    if ($allowedColumns === null) {
      return $data;
    }

    return array_intersect_key($data, array_flip($allowedColumns));
  }

  private function identifier($identifier) {
    if (!preg_match('/^[A-Za-z0-9_]+$/', $identifier)) {
      $this->error('Unsafe SQL identifier');
    }

    return "`" . $identifier . "`";
  }

}
?>
