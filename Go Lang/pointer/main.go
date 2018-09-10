package main

import (
	"fmt"
)

func main() {
	var a int = 10
	var b *int
	var ptr *int

	// a的指针地址与b的指针地址相同
	b = &a

	fmt.Printf("a变量的地址%x\n", &a)

	fmt.Printf("b变量存储的指针地址%x\n", b)

	// b的值=a的值，因为两者皆指向同一个存储地址
	fmt.Printf("b变量的值%d\n", *b)

	// 指针为空判断
	if ptr == nil {
		fmt.Printf("ptr为空指针,地址为:%x\n", ptr)
	} else {
		fmt.Printf("ptr不为空指针,地址为:%x\n", ptr)
	}
}
