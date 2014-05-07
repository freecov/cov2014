CFLAGS = "-g"

parser: parser.l
	flex -oparser.lex.c parser.l
	gcc -o $@ $(CFLAGS) parser.lex.c
	
