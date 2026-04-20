# UiTM-ARCHIEVE-SYSTEM.MD

# MENU

---

## NO. RUJUKAN (FIRST PAGE)
- display all registered in below table format
|BIL.|NO. RUJUKAN|PERKARA|

### DAFTAR NO. RUJUKAN (SINGLE) (DAFTAR BUTTON-PART OF FIRST PAGE)
- forms0()
| SIRI: FILL THE BLANK---e.g 100---rules positive number only
| KAMPUS: UiTM<FILLTHEBLANK>---e.g UiTM---rule default all uppercase unless explicitely intentional, KAMPUS inherit from profile, fill the blanks for variants & optional 
| KOD BAHAGIAN: FILL THE BLANK---e.g INFO---rules all uppercase
| NOMBOR FAIL: FILL THE BLANK---e.g 1/1---rules no word
| PERKARA: FILL THE BLANK---e.g PENTADBIRAN - AM---rules all uppercase
| DISPLAY NO. RUJUKAN FULL FORMAT: 100-UiTM (INFO. 1/1) | 100-UiTM(INFO. 1/1)
| ADDITIONAL SPACE: ENABLE | DISABLE

### DAFTAR NO. RUJUKAN (BATCH) (BATCH BUTTON-PART OF FIRST PAGE)
- forms0() downloadable template in .csv
- upload the .csv---when template format not respected rejects it & tell the reason why

### DELETE NO. RUJUKAN (SELECT BUTTON-PART OF FIRST PAGE)
- select row to delete

---

## FAIL (SECOND PAGE)
- display all registered in below table format
|BIL.|NO. RUJUKAN|PERKARA|JILID|TARIKH KANDUNGAN PERTAMA|TARIKH KANDUNGAN AKHIR|TARIKH TUTUP|KOTAK|PERSON IN CHARGE|

### DAFTAR FAIL (DAFTAR BUTTON-PART OF SECOND PAGE)
- forms1()
| NO. RUJUKAN: DROPDOWN---rules compulsory
| JILID: FILL THE BLANK---e.g 1---rule no word,compulsory, positive number only, unique(each NO. RUJUKAN cant have duplicate JILID)
| TARIKH KANDUNGAN PERTAMA: CALENDAR---rules compulsory
| PERSON IN CHARGE: INHERIT CURRENT ACCOUNT NAME

### DAFTAR FAIL (BATCH) (BATCH BUTTON-PART OF SECOND PAGE)
- forms1() downloadable template in .csv
- upload the .csv---when template format not respected rejects it & tell the reason why

### KEMASKINI FAIL (CLICK A ROW-PART OF SECOND PAGE)
- forms1()
| NO. RUJUKAN: inherit---rules read-only
| JILID: inherit---rules read-only
| TARIKH KANDUNGAN PERTAMA: inherit---rules read-only
| TARIKH KANDUGAN AKHIR: CALENDAR
| TARIKH TUTUP: CALENDAR
| KOTAK: FILL THE BLANK---e.g 1---rules no word
| PERSON IN CHARGE: INHERIT CURRENT ACCOUNT NAME

### DELETE FAIL (SELECT BUTTON-PART OF SECOND PAGE)
- select row to delete

---

## PEMISAHAN REKOD (THIRD PAGE)
- inherit second page's registered data in below table format
- grouped by NO. KOTAK because each files inside its own box
- NO. FAIL = NO. RUJUKAN, TAJUK FAIL = PERKARA & JILID, TARIKH BUKA = TARIKH KANDUNGAN PERTAMA, TARIKH TUTUP = TARIKH KANDUNGAN AKHIR, NO. KOTAK = KOTAK
|BIL.|NO. FAIL|TAJUK FAIL|TARIKH PERMISAHAN|TARIKH BUKA|TARIKH TUTUP|TUJUAN PEMISAHAN|NO. KOTAK|PERSON IN CHARGE|

### KEMASKINI REKOD (CLICK A ROW-PART OF THIRD PAGE)
- forms2()
| TARIKH PEMISAHAN: CALENDAR---rules compulsory
| TUJUAN PEMISAHAN: FILL THE BLANK---rules compulsory
| PERSON IN CHARGE: INHERIT CURRENT ACCOUNT NAME

### PRINT (SELECT BUTTON-PART OF THIRD PAGE)
- select row to print---rules can select a box at time

#### PRINT PEMISAHAN REKOD (PEMISAHAN REKOD-PART OF SELECT BUTTON OF THIRD PAGE)
- Kampus/ Bahagian/ Fakulti/ Pusat: Inherit---rule inherit from profile
- use the following template [./res/borangPemisahanRekod.doc]

#### PRINT PENTADBIRAN (PENTADBIRAN BUTTON-PART OF THIRD PAGE)
- inherit No. Kotak = NO. KOTAK, Fakulti/Bahagian(profile), Cawangan(profile), Tahun Rekod = tahun tarikh buka & tutup (e.g 2013/2020), Jumlah Fail = total fail in a box, Bil Fail = NO. FAIL & TAJUK FAIL
- use the following template [./res/labelFailPentadbiran.png]

#### PRINT STAF (PENTADBIRAN BUTTON-PART OF THIRD PAGE)
- inherit No. Kotak = NO. KOTAK, Fakulti/Bahagian(profile), Cawangan(profile), Tahun Rekod = tahun tarikh buka & tutup (e.g 2013/2020), Jumlah = total fail in a box, Bil Fail = NO. FAIL & TAJUK FAIL
- use the following template [./res/labelFailStaf.png]

#### PRINT PELAJAR (PENTADBIRAN BUTTON-PART OF THIRD PAGE)
- inherit No. Kotak = NO. KOTAK (e.g 001), Fakulti(profile), Tahun = tahun tarikh buka & tutup (e.g 2013/2020), Jumlah Fail = total fail in a box
- use the following template [./res/labelFailPelajar.png]

---

## PELUPUSAN (FOURTH PAGE)
- inherit third page's registered data in below table format
- grouped by NO. KOTAK because the files inside its own box
- pending table for lupus
|BIL.|NO. FAIL|TAJUK FAIL|NO. KOTAK|STATUS|PERSON IN CHARGE|

- table after lupus
|BIL.|NO. KOTAK|PERSON IN CHARGE|

### PRINT (SELECT BUTTON-PART OF FOURTH PAGE)
- select row to print---rules can select a box at time

#### PRINT PULUPUSAN REKOD (PELUPUSAN-PART OF FOURTH PAGE)
- inherit FAKULTI/BAHAGIAN/PUSAT/UNIT/CAWANGAN(profile), Tarikh permohonan(current date), tahun(tahun tarikh buka & tutup), jumlah(total fail in a box)
- use the following template [./res/borangPelupusanRekod.jpg]

### STATUS COLUMN (PART OF FOURTH PAGE)
- DROPDOWN PENDING(DEFAULT) , APPROVE, DECLINE

### LUPUS (CLICK A ROW-PART OF FOURTH PAGE)
- select a row to lupus
- it shows in table after lupus  

---

## USER MANAGEMENT

### SUPERADMIN
- admin@uitm.edu.my
- default: password
- add & delete user
- reset user password to default 'password'
- manage user position position PTRJ & PRJ---rules one position cant have multiple account

### REGISTER
- FULLNAME: ---rules all uppercase
- EMAIL: ---rules strict to @uitm.edu.my & all lowercase
- CREATE PASSWORD: ---rules strict to 8 characters
- KAMPUS: e.g UiTM---rules default all uppercase unless explicitely intentional
- CAWANGAN: e.g SEGAMAT---rules 
- FAKULTI/BAHAGIAN ---rules all uppercase
- POSITION: ---rules dropdown PTRJ/PRJ(one position cant have multiple account)

### LOGIN
- registered email
- password
- forgot password

### USER PROFILE
- DISPLAY FULLNAME
- DISPLAY EMAIL
- DISPLAY KAMPUS
- DISPLAY CAWANGAN
- DISPLAY FAKULTI/BAHAGIAN
- DISPLAY POSITION
- CHANGE PASSWORD---rules CURRENT & NEW password 
- TRANSFER POSITION TO ANOTHER USER

#### INHERIT

#### PRINT PEMISAHAN REKOD
- dropdown to inherit from KAMPUS | CAWANGAN | FAKULTI/BAHAGIAN from profile

#### PRINT PULUPUSAN REKOD
- dropdown to inherit from KAMPUS | CAWANGAN | FAKULTI/BAHAGIAN from profile

---

# DONT DELETE OR REMOVE THIS FILE REQURIE FOR FUTURE REFERENCES & MODIFICATION


























