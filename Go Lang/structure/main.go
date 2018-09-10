package main

import (
	"fmt"
)

type Books struct {
	title    string
	author   string
	category string
	book_id  int
}

func main() {
	var Book1 Books
	var Book2 Books
	// Book1
	Book1.title = "Laravel 5.4"
	Book1.author = "admin"
	Book1.category = "PHP框架"
	Book1.book_id = 100001

	// Book2
	Book2.title = "Go 语言手册"
	Book2.author = "golang"
	Book2.category = "计算机软件"
	Book2.book_id = 200001

	// 打印book1
	outBook(Book1)

	// 打印book2
	outBook(Book2)
}

func outBook(book Books) {
	fmt.Printf("Book title : %s\n", book.title)
	fmt.Printf("Book author : %s\n", book.author)
	fmt.Printf("Book category : %s\n", book.category)
	fmt.Printf("Book book_id : %d\n", book.book_id)
}
