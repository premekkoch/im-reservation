# IM-reservation 
## Zadání úkolu
Vytvořte jednoduchý rezervační systém pro konferenční místnosti. Systém by měl umožňovat uživatelům registraci, 
přihlášení a rezervaci konferenčních místností na určitý časový úsek.

## Analýza
Řešení musí být zpracováno tak, aby výsledné řešení bylo uživatelsky přívětivé a přehledné a zároveň jeho 
implementace byla jednoduchá a realizovatelná v stanoveném časovém úseku 2 hodin.

Při analýze požadavku jsem se zaobíral dvěmi uživatelskými scénáři:

1. Uživatel potřebuje uspořádat meeting v daném čase a zbývá vybrat místnost, kde je volno (stežejní je čas 
a místo konání není důležité).


2. Uživatel potřebuje konkrétní místnost a čas schůzky přizpůsobí dle obsazenosti této místnosti (stěžejní je 
místnost a čas konání schůzky je podružný).

Z implementačního pohledu jsem se zabýval zejména způsobem, jak nakládat s rezervačními záznamy v databázi 
ve vztahu k nové rezervaci, tzn. zejména hledání volných časových oken a algoritmy pro zjišťování (resp.
zamezení) překryvu časů.

V podtaz jsem také vzal chování uživatelů v reálném světě, kdy rezervace místnosti s granularitou na minuty 
není potřebná a spíše nežádoucí a obtěžující (vyplňování datetime políček). Dále jsem vycházel z předpokladu,
že řešení by mělo být co nejpřehlednější a jednoduché na používání.

## Implementace
Mnou navržené řešení má tedy s ohledem na požadavky v zadání následující zásadní vlastnosti:

- Výběr konkrétní místnosti má prioritu před výběrem času. Rozhodl jsem se pro toto řešení, jelikož tento scénář
je častější v mém současném zaměstnání.

 
- Den je rozdělen do časových úseků (slotů), jejichž délka je nastavena na 30 minut. Délku časového úseku [lze 
měnit](https://github.com/premekkoch/im-reservation/blob/master/app/Services/SlotService.php#L12), nicméně z 
důvodu zachování konzistence uložených záznamů není tato změna umožněna parametricky (se změnou délky slotu je 
potřeba modifikovat stávající databázové záznamy rezervací).


- Sloty, které lze v rámci dne rezervovat, jsou z důvodu příjemnější obsluhy omezeny na dobu 6:00 - 19:00 hodin. 
Obě tyto ["zarážky"](https://github.com/premekkoch/im-reservation/blob/master/app/Services/SlotService.php#L13) 
lze opět programátorsky měnit. 


- Každý časový slot je indexován svým pořadím v rámci dne. V databázi je pak uložen index slotu začátku rezervace 
a délka rezervace je vyjádřena počtem za sebou jdoucích slotů. Takovéto řešení poskytuje možnost se elegantně 
vyhnout "složitému" vzájemnému porovávání časových údajů stávajících a nově zakládaných rezervací.


- Každý slot má dva časové údaje (čas začátku a čas konce), které se používají dle úhlu pohledu na tento daný
slot (pořáteční čas nové rezervace vs. konečný čas nové rezervace). Tyto dva časové údaje jsou [generovány 
dynamicky](https://github.com/premekkoch/im-reservation/blob/master/app/Services/SlotService.php#L21). Každá 
databázová entita rezervace obsahuje časovou informaci o začátku a konci v podobě [virtuálních 
property](https://github.com/premekkoch/im-reservation/blob/master/app/Model/Reservation/Reservation.php#L24),
které jsou nastavovány stejným algoritmem.


- Při [získání volných slotů pro začátek nové rezervace](https://github.com/premekkoch/im-reservation/blob/master/app/Services/SlotService.php#L35)
vycházím z kompletní nagenerované tabulky (pole) všech slotů konkrétního dne, ze kterých odstraňuji ty sloty, 
u kterých existuje v databázi rezervační záznam se stejným indexem slotu. Výsledkem je indexované pole obsahující 
záznamy slotů, ve kterých může začít nová rezervace.


- Na základě indexu počátečního slotu jsem následně schopen jednoduše [získat pole časově navazujících 
slotů](https://github.com/premekkoch/im-reservation/blob/master/app/Services/SlotService.php#L51), které nejsou 
rezervovány. Opět vycházím z nagenerované tabulky (tentokrát až od indexu počátečního slotu do konce
dne), ze kterých odstraňuji všechny vyšší indexy než index je index případné již existující nejbližší rezervace. 
Výsledkem je pak pole obsahující všechny souvislé nerezervované sloty od počátečního slotu včetně. 


- Homepage je přístupná pouze pro přihlášené uživatele a obsahuje všechna potřebná data a funcionalitu - filtr 
podle dne a místnosti, seznam všech rezervací dané místnosti v daném dni s možností smazat "vlastní" rezervace a
formulář pro založení nové rezervace dané místnosti v daném dni (pro založení nové rezervace je potřeba jen vybrat
časový údaj začátku a konce rezervace).


- Formulář nové rezervace je jako jediný zpracován dynamicky, a to jen v minimálním potřebném rozsahu (a s minimálním 
úsilím). Jeho implementace je jedním z horkých kandidátů na budoucí refaktoring do podoby komponenty (z důvodu 
zapouzdření funkcionality).


- Registrace nového uživatele a přihlášení uživatele je implementovano pouze "naivně" tak, aby byla splněna
požadovaná funkcionalita.


- Presentery obsahují minimum logiky; s přihlédnutím na rozsah implementace jsem operace s databázovými entitami
implementoval [přímo do presenterů](https://github.com/premekkoch/im-reservation/blob/master/app/UI/Home/HomePresenter.php#L117).
Vnitřné cítím, že zejména u entity rezervace je to "už příliš" a dalším kandidátem 
na refaktoring je zapouzdření operací s touto entitou do vlastního manageru.


- Služba "SlotService" je oproti tomu navržená jako nezávislá, zapouzřující kompletní (kritickou) funkcionalitu
se sloty. Je tedy horkým kandidátem na pokrytí testy, což jsem z časových důvodů neiplemenoval.


- Jako skeleton projektu jsem použil předpřipravený [nette/web-project](https://github.com/nette/web-project).
Adresářovou strukturu tohoto skeletonu jsem zachoval, ačkoliv je mi bližší ["starší"](https://github.com/premekkoch/demo)
rozvržení struktury projektu.


- Důraz na frontend aplikace jsem kladl minimální. Řešení problematiky časových zón jsem úmyslně vynechal. 

## Závěr
Úkol je dle mého názoru velice chytře zvolen - na první pohled jednoduchá záležitost, která však obsahuje několik
pastí - jak z pohledu implementace, tak UX - pokud se nepodchytí analýzou. Myslím, že v rámci mého řešení 
se mi podařilo se jim vyhnout. Při implementaci úkolu jsem s v některých bodech odchýlil od zadání; důvody jsem se 
snažil nastínit v předchozím textu.

Zadaný čas na vypracování úkolu jsem překročil cca o 2 hodiny čistého času vlivem delší analýzy, nepřipravenosti 
vývojového prostředí a vypadnutí z rutiny PHP developmentu. Zpracovaní této dokumentace do tohoto času 
nezapočítávám.
