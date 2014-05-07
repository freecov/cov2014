/*
 *  Copyright (C) 2006 Svante Kvarnstrom
 *
 *  This program is free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program; if not, write to the Free Software
 *  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307
 *  USA
 */

/* 
 * This program was created with the help of Jonas Berlin
 */

/*-*-c-*-*/

%{
#include <stdio.h>
#include <string.h>
char *unescape(const char *);
void process(int, char **, const char *);
char *escape(const char *);
void dosql(char **);
%}

%%

\n				  { return 10; } 
,				  { return 11; }
[^\",\n]+		  { return 12; }
\"(\"\"|[^\"])*\" { return 13; }

%%

int yywrap() { return 1; }

/* {{{ main() */
/**
 * TODO single line description
 *
 * TODO multiline description
 *
 * @param int Number of arguments
 * @param char** Array of pointers to args
 */
int main(int argc, char **argv) {	
	char *unescaped = NULL;
	char *fields[2000];
	int fieldnr = 0;
	int quit = 0;
	while (!quit)  {
		switch (yylex()) {
			case 0:
				quit = 1;
				break;
			case 10:
				/* new row */
				dosql(fields); 
				fields[10] = NULL; /* Need to make sure this one'll be null - will be reallocating */
				fieldnr = 0;
				break;
			case 11:
				/* new field */
				fieldnr++;
				break;
			case 12: 
				/* simple field */
				process(fieldnr, fields, yytext);
				break; 
			case 13:
				/* 'complex' field */
				unescaped = unescape(yytext);
				process(fieldnr, fields, unescaped);
				free(unescaped);
				break;
		}	
	}
	return 0;
} /* }}} */
/* {{{ dosql() */
/**
 * Print sql.
 *
 * This function uses the fields array of pointers to create an ready-to-paste SQL line.
 *
 * @param char* Array of pointers --> field values
 * @return void
 */
void dosql(char *fields[]) {	
	printf("INSERT INTO nrd_consultant (title,firstname,surname,address,"
		"address2,zipcode,city,country,email,phone_nr,mobile_nr,ssn,other) VALUES "
		"('%s', '%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s');\n", 
		escape(fields[0]), escape(fields[1]), escape(fields[2]), escape(fields[3]),
		escape(fields[4]), escape(fields[6]), escape(fields[5]), escape(fields[7]),
		escape(fields[11]), escape(fields[8]), escape(fields[9]), escape(fields[72]),
		escape(fields[10]));
	/* Nullify everything */
	fields[0] = NULL;
	fields[1] = NULL;
	fields[2] = NULL;
	fields[3] = NULL;
	fields[4] = NULL;
	fields[5] = NULL;
	fields[6] = NULL;
	fields[7] = NULL;
	fields[8] = NULL;
	fields[9] = NULL;
	fields[10] = NULL;
	fields[11] = NULL;
	fields[72] = NULL;
	bzero(fields, sizeof(fields));
} /* }}} */
/* {{{ process() */
/** 
 * Process yytext
 * 
 * This function processes yytext and copies the interesting stuff to fields[n].
 * 
 * @param int Id of field.
 * @param char Array of pointers which'll hold the contents of certain fields.
 * @param const char yytext (from flex)
 * @return no return
 */
void process(int id, char *fields[], const char *yytext) {
	const char *colnames[] = {  "titel",  "fornamn", "mellannamn", "efternamn",
		"namnsuffix", "foretag", "avdelning", "befattning", "adress, arbete", 
		"adress2, arbete", "adress3, arbete", "ort, arbete", "region, arbete", 
		"postadress, arbete", "land, arbete", "gatuadress, hem", "gatuadress 2, hem", 
		"gatuadress 3, hem", "hemort", "region, hem", "postnr, hem", "land, hem", 
		"annan gata",  "annan gata 2", "annan gata 3", "annan ort", "annan region", 
		"annat postnummer", "annat land", "telefon, assistent",  "fax arbete",  
		"arbete", "telefon 2, arbete", "motringning",  "biltelefon", "foretag, vaxel",
		"fax, hem", "hem", "telefon 2, hem", "ISDN",  "mobiltelefon",  "fax, annan", 
		"telefon, annan", "personsokare", "telefon 1",  "telefon 2", "TTY/TDD-telefon",
		"Telex",  "Annan adress, box", "Anteckningar", "Anvandare",  "Anvandare 2", 
		"Anvandare 3", "Anvandare 4", "Arbetsadress", "Arbetsplats", "Barn", "e-postadress", 
		"e-posttyp", "e-postnamn", "e-postadress 2", "e-posttyp 2", "e-postnamn 2", 
		"e-postadress 3", "e-posttyp 3", "e-postnamn 3", "fakturering", "fodelsedag", 
		"hemadress, box", "hobby", "initialer", "katalogserver", "kategorier", "konto", 
		"kanslighet", "kon", "ledig/upptagen-information for internet", "make/maka", 
		"Namn, assistent", "Namn, chef", "Nyckelord", "organisationsnummer", "personnummer", 
		"plats", "prioritet", "privat", "referens", "reseersattning", "sprak", "webbsida", 
		"yrke", "arsdag"
	};

	if (id == 0) { 
//		printf("Titel: %s\n", yytext);
		fields[0] = strdup(yytext);
	}
	else if (id == 1) {
		fields[1] = strdup(yytext);
//		printf("Fornamn: %s\n", yytext);
	}
	else if (id == 3) {
		fields[2] = strdup(yytext);
//		printf("Efternamn: %s\n", yytext);
	}
	else if (id == 15) {
		fields[3] = strdup(yytext);
//		printf("Adress, hem: %s\n", yytext);
	}
	else if (id == 16) {
		fields[4] = strdup(yytext);
//		printf("Adress2, hem: %s\n", yytext);
	}
	else if (id == 18) {
		fields[5] = strdup(yytext);
//		printf("Hemort: %s\n", yytext);
	}
	else if (id == 20) {
		fields[6] = strdup(yytext);
//		printf("Postnummer: %s\n", yytext);
	}
	else if (id == 21) {
		fields[7] = strdup(yytext);
//		printf("Land: %s\n", yytext);
	}
	else if (id == 37) {
		fields[8] = strdup(yytext);
//		printf("Telefon hem: %s\n", yytext);
	}
	else if (id == 40) {
		fields[9] = strdup(yytext);
//		printf("Mobiltelefon: %s\n", yytext);
	}
	else if (id == 49) {
		fields[10] = realloc(fields[10], (fields[10] == NULL ? 0 : strlen(fields[10])) + strlen(yytext) + 1);
		fields[10] = strcpy(fields[10], yytext);
		
//		printf("Anteckningar: %s\n", yytext);
			
	}
	else if (id == 57) {
		fields[11] = strdup(yytext);
//		printf("e-postadress: %s\n", yytext);
	}
	else if (id == 72) {
		fields[12] = strdup(yytext);
//		printf("personnummer: %s\n", yytext);
	}
	else if (id == 58) {
		fields[13] = strdup(yytext);
//		printf("email: %s\n", yytext);
	} else {
//		printf("%d (%s): %s\n", id, colnames[id], yytext);
		
		int oldlen = fields[10] == NULL ? 0 : strlen(fields[10]); /* fields[10] length before the change */
		int newlen = (fields[10] == NULL ? 0 : strlen(fields[10]) + strlen(colnames[id]) + strlen(yytext) + 3); /* length of fields[10] after we're done with it */
		fields[10] = realloc(fields[10], oldlen + newlen);
		snprintf(fields[10] + oldlen, newlen, "%s: %s\n", colnames[id], yytext);
	}
} /* }}} */
/* {{{ escape() */
/**
 * Escapes string
 *
 * This function loops through the given const char * and prepends \'s to
 * ''s.
 *
 * @param const char String to escape
 * @return Escaped malloced string
 */
char *escape(const char *data) {
	if (data == NULL)
		return "";

	char *escaped = malloc(strlen(data) * 2); /* Let's exaggerate */
	char *start = escaped; /* start of data */
	const char *end = data + strlen(data); /* end of data */
	
	while (data < end) {
		if (*data == '\'') {
			*escaped++ = '\'';
		}
		*escaped++ = *data++;
	}
	*escaped++ = '\0';
	return start;
} /* }}} */
/* {{{ unescape() */
/**
 * Unescapes string.
 *
 * This function loops through the given const char * and removes any \'s in front
 * of ''s.
 *
 * @param const char String to unescape
 * @return Unescaped malloced string.
 */
char *unescape(const char *escaped) {
  const char *s = escaped + 1;
  const char *e = s + strlen(s) - 1;
  char *unescaped = malloc(e - s + 1);
  char *d = unescaped;

  while(s < e) {
	if(*s == '"') 
	  s++;
	*d++ = *s++;
  }
  *d = '\0';
  return unescaped;
} /* }}} */
