package main

import (
	_ "gf-jinzhiyv/internal/packed"

	"github.com/gogf/gf/v2/os/gctx"
	"gf-jinzhiyv/internal/cmd"
)

func main() {
	cmd.Main.Run(gctx.New())
}
