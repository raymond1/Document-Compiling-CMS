package main

import (
	"fmt"
	"os"
)

func main() {
	content, err := os.ReadFile("script.txt")
	if err != nil {
		fmt.Printf("Missing script.txt file.\n")
		os.Exit(0)
	}

	fmt.Printf(string(content))
}
