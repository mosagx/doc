package main

import (
	"fmt"
)

func main() {
	nums := []int{2, 3, 4}
	sum := 0
	for _, num := range nums {
		sum += num
	}
	fmt.Println("sum:", sum)

	// 索引
	for i, num := range nums {
		if num == 3 {
			fmt.Println("index:", i)
		}
	}

	// map键值对
	kvs := map[string]string{"a": "apple", "b": "banana"}
	for k, v := range kvs {
		fmt.Printf("%s -> %s \n", k, v)
	}

	// 枚举Unicode字符串
	for i, c := range "test" {
		fmt.Println(i, c)
	}
}
