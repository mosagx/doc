package main

import (
	"fmt"
)

func main() {
	numbers := []int{0, 1, 2, 3, 4, 5, 6, 7, 8, 9}
	printSlice(numbers)

	// 打印原始切片
	fmt.Println("numbers ==", numbers)

	// 打印子切片
	fmt.Println("numbers[1:4] ==", numbers[1:4])

	fmt.Println("numbers[:3] ==", numbers[:3])

	fmt.Println("numbers[4:] ==", numbers[4:])

}

func printSlice(x []int) {
	fmt.Printf("len=%d cap=%d slice=%v\n", len(x), cap(x), x)
}
