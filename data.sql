CREATE TABLE `prescription_books` (
id int(8) NOT NULL AUTO_INCREMENT,
patient_id int(8) NOT NULL,
doctor_id int(8) NOT NULL,
date datetime NOT NULL,
PRIMARY KEY (id),
FOREIGN KEY (patient_id) REFERENCES users(id),
FOREIGN KEY (doctor_id) REFERENCES users(id)
)

CREATE TABLE `recipe` (
id int(8) NOT NULL AUTO_INCREMENT,
prescription_book_id int(8) NOT NULL,
date datetime NOT NULL,
received_date datetime NOT NULL,
pharmacist_id int(8) NOT NULL,
additinal_information varchar(512) NULL,
PRIMARY KEY (id),
FOREIGN KEY (pharmacist_id) REFERENCES users(id),
FOREIGN KEY (prescription_book_id) REFERENCES prescription_books(id)
)

CREATE TABLE `drugs` (
id int(8) NOT NULL AUTO_INCREMENT,
`name` varchar (128) NOT NULL,
PRIMARY KEY (id)
)

CREATE TABLE `reciple_drugs` (
id int(8) NOT NULL AUTO_INCREMENT,
recipe_id int(8) NOT NULL,
drug_id int(8) NOT NULL,
quantity int(2) NOT NULL,
PRIMARY KEY (id),
FOREIGN KEY (recipe_id) REFERENCES recipe(id),
FOREIGN KEY (drug_id) REFERENCES drugs(id)
)

CREATE TABLE `inquiries` (
id int(8) NOT NULL AUTO_INCREMENT,
inquiry varchar(512) NOT NULL,
reply varchar(512) NULL,
user_id int(8) NOT NULL,
date datetime NOT NULL,
PRIMARY KEY (id),
FOREIGN KEY (user_id) REFERENCES users(id)
)