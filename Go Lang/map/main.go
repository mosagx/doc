package main

import (
	"fmt"
)

func main() {
	countryCapMap := map[string]string{"France": "Paris", "China": "Beijin", "Japan": "Tokyo", "Italy": "Rome"}
	fmt.Println("原始地图")

	// 打印元素
	outItem(countryCapMap)

	// 删除Italy
	delete(countryCapMap, "Italy")
	fmt.Println("删除Italy后的地图")

	// 打印元素
	outItem(countryCapMap)
}

func outItem(data map[string]string) {
	for country := range data {
		fmt.Println("The ", country, "Captain is ", data[country])
	}
}
