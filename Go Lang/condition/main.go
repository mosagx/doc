package main

import (
	"fmt"
)

func main() {
	var count int
	var flag bool
	count = 1
	_ = flag
	for count < 100 {
		count++
		flag = true
		for tmp := 2; tmp < count; tmp++ {
			if count%tmp == 0 {
				flag = false
			}
		}

		if false == true {
			fmt.Printf("素数:%d\n", count)
		} else {
			continue
		}
	}
}
