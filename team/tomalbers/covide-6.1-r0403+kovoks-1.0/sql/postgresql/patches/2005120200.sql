ALTER TABLE notitie ADD subject character varying(255);
UPDATE notitie SET subject=onderwerp;
ALTER TABLE notitie DROP onderwerp;
