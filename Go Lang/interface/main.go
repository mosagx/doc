package main

import (
	"fmt"
)

type People interface {
	name() string
	age() int
}

type Woman struct {
}

func (woman Woman) name() string {
	return "Sandy"
}

func (woman Woman) age() int {
	return 21
}

type Men struct {
}

func (mem Men) name() string {
	return "Jack"
}

func (men Men) age() int {
	return 23
}

func main() {
	var people People
	people = new(Woman)
	fmt.Println(people.name(), people.age())
	people = new(Men)
	fmt.Println(people.name(), people.age())
}
