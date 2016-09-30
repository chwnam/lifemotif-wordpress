<?php

namespace lmwp\models;


class DiemLocalDatabase
{
    private $handler = null;

    public function __construct($dbPath)
    {
        $this->handler = new \SQLite3($dbPath, SQLITE3_OPEN_READONLY);
    }

    public function getHandler()
    {
        return $this->handler;
    }

    public function getIndexCount()
    {
        if ($this->handler) {
            $query = 'SELECT COUNT(*) FROM diem_id_index';

            return $this->handler->querySingle($query);
        }

        return 0;
    }

    public function queryIndexList($minMid)
    {
        if ( ! $this->handler) {
            return null;
        }

        // get a list of (mid, tid, diary_date) that each mid > $minMid
        $query = "
			SELECT
				id_index.mid AS mid,
				id_index.tid AS tid,
				date_index.diary_date AS diary_date
			FROM
				diem_id_index AS id_index
			INNER JOIN diem_date_index AS date_index
				ON id_index.tid = date_index.tid
			WHERE
				id_index.mid > :mid			
		";

        $statement = $this->handler->prepare($query);
        $statement->bindValue(':mid', $minMid);

        return $statement->execute();
    }
}
