package main

import (
	"fmt"
)

const LINES int = 10

func main() {
	// res := max(2, 3)
	// fmt.Printf("最大值为%d", res)
	Multiplication()
}

// 最大值
func max(num1, num2 int) int {
	var result int
	if num1 > num2 {
		result = num1
	} else {
		result = num2
	}
	return result
}

// 杨辉三角
func YangHuiTriangle() {
	nums := []int{}
	for i := 0; i < LINES; i++ {
		// 空白
		for j := 0; j < (LINES - i); j++ {
			fmt.Print(" ")
		}

		for j := 0; j < (i + 1); j++ {
			var length = len(nums)
			var value int
			if j == 0 || j == i {
				value = 1
			} else {
				value = nums[length-i] + nums[length-i-1]
			}
			nums = append(nums, value)
			fmt.Print(value, " ")
		}

		fmt.Print("\n")
	}
}

// 九九乘法
func Multiplication() {
	// row
	for i := 1; i < 10; i++ {
		for j := 1; j <= i; j++ {
			fmt.Print(j, " * ", i, " = ", j*i, "  ")
		}
		fmt.Print("\n")
	}
}
