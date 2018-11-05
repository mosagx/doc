package main

import (
	"database/sql"
	"fmt"
	"strings"

	_ "github.com/go-sql-driver/mysql"
)

const (
	db_user = "root"
	db_pwd  = "root"
	db_host = "127.0.0.1"
	db_port = "3306"
	db_name = "test"
)

type mysql_db struct {
	db *sql.DB
}

func (f *mysql_db) mysql_open() {
	path := strings.Join([]string{db_user, ":", db_pwd, "@tcp(", db_host, ":", db_port, ")/", db_name, "?charset=utf8"}, "")
	Odb, err := sql.Open("mysql", path)
	if err != nil {
		fmt.Println("connect field!")
	}
	fmt.Println("connect success!--------------- start")
	f.db = Odb
}

func (f *mysql_db) mysql_close() {
	defer f.db.Close()
	fmt.Println("connect success!--------------- close")
}

func (f *mysql_db) mysql_query(sql_str string) {
	rows, err := f.db.Query(sql_str)
	if err != nil {
		fmt.Println(err)
	}

	for rows.Next() {
		var row1 string
		var row2 string
		var row3 string
		err = rows.Scan(&row1, &row2, &row3)
		if err != nil {
			panic(err)
		}

		fmt.Println(row1 + "  " + row2 + "  " + row3)
	}
}

func main() {
	db := &mysql_db{}
	db.mysql_open()
	db.mysql_query("SELECT * FROM stu")
	db.mysql_close()
}
