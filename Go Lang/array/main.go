package main

import (
	"fmt"
)

func main() {
	var n [10]int

	for i := 0; i < 10; i++ {
		n[i] = i + 100
	}

	for j := 0; j < 10; j++ {
		fmt.Printf("Item[%d] = %d\n", j, n[j])
	}
}
