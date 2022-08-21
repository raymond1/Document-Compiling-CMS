package main

import (
	"fmt"
	"os"
)

func main() {
	script_file := "script.txt"
	if (len(os.Args) == 2){
		script_file = os.Args[1]
	}

	content, err := os.ReadFile(script_file)
	if err != nil {
		fmt.Printf("Missing script.txt file.\n")
		os.Exit(0)
	}

	fmt.Printf(string(content))
}
