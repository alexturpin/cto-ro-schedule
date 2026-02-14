# cto-ro-schedule

## Deployment

With `lftp` installed (`brew install lftp`), run:

```bash
./deploy.sh
```

## Google Calendar integration

ctoutaouais@gmail.com has three calendars

- Membre (personal, shown in blue on the website)
- [Officiels CTO: À combler](https://horaire.clubtiroutaouais.ca/google.php?combler=true) (slots to fill, shown in red)
- [Officiels CTO](https://horaire.clubtiroutaouais.ca/google.php?combler=false) (filled slots, shown in green)

This iframe code displays all 3 on clubtiroutaouais.ca

```html
https://calendar.google.com/calendar/embed?height=600&wkst=1&ctz=America%2FToronto&showPrint=0&src=Y3RvdXRhb3VhaXNAZ21haWwuY29t&src=Yzg3MjlrZmpiNHZpcjAzanYwaDhtMW8zZGZ0ZmY2bTJAaW1wb3J0LmNhbGVuZGFyLmdvb2dsZS5jb20&src=bGRicHEybWhzNWJka3Eyc3ZsbGxsZzBwZWdxa2Vva2NAaW1wb3J0LmNhbGVuZGFyLmdvb2dsZS5jb20&color=%23039be5&color=%23009688&color=%23e67c73
```
