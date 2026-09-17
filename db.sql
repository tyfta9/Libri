-- creating tables

-- users table
CREATE TABLE IF NOT EXISTS users (
    username VARCHAR(30) PRIMARY KEY,
    password VARCHAR(30) NOT NULL,
    fname VARCHAR(50),
    sname VARCHAR(50),
    address1 VARCHAR(100),
    address2 VARCHAR(100),
    city VARCHAR(30),
    tel VARCHAR(20),
    mobile VARCHAR(20) NOT NULL
);

-- book's categories table
CREATE TABLE IF NOT EXISTS categories (
    id VARCHAR(3) PRIMARY KEY,
    description VARCHAR(50) UNIQUE NOT NULL
);

-- books table
CREATE TABLE IF NOT EXISTS books (
    isbn VARCHAR(20) PRIMARY KEY,
    title VARCHAR(50) NOT NULL,
    author VARCHAR(100),
    edition INT,
    year INT,
    category VARCHAR(3) REFERENCES categories(id),
    resell BOOLEAN NOT NULL
);

-- reserved books table
CREATE TABLE IF NOT EXISTS reservedBooks (
    isbn VARCHAR(20) REFERENCES books(isbn),
    username VARCHAR(30) REFERENCES users(username),
    date VARCHAR(20) NOT NULL
);

-- populating database 

-- insert sample data into categories table
INSERT INTO categories (id, description) VALUES
('001', 'Health'),
('002', 'Business'),
('003', 'Biography'),
('004', 'Technology'),
('005', 'Travel'),
('006', 'Self-Help'),
('007', 'Cookery'),
('008', 'Fiction');

-- insert sample data into users table
INSERT INTO users (username, password, fname, sname, address1, address2, city, tel, mobile) VALUES
('alanjmckenna', 't1234s', 'Alan', 'McKenna', '38 Cranley Road', 'Fairview', 'Dublin', '9998377', '856625567'),
('joecrotty', 'kj7899', 'Joseph', 'Crotty', 'Apt 5 Clyde Road', 'Donnybrook', 'Dublin', '8887889', '876654456'),
('tommy100', '123456', 'Tom', 'Behan', '14 Hyde Road', 'Dalkey', 'Dublin', '9983747', '876738782');

-- insert sample data into books table
INSERT INTO books (isbn, title, author, edition, year, category, resell) VALUES
('093-403992', 'Computers in Business', 'Alicia Oneill', 3, 1997, '003', false),
('23472-8729', 'Exploring Peru', 'Stephanie Birchi', 4, 2005, '005', false),
('237-34823', 'Business Strategy', 'Joe Peppard', 2, 2002, '002', false),
('2318-923849', 'A guide to nutrition', 'John Thorpe', 2, 1997, '001', false),
('2983-3494', 'Cooking for children', 'Anabelle Sharpe', 1, 2003, '007', false),
('8218-308', 'computers for idiots', 'Susan O''Neill', 5, 1998, '004', false),
('9823-23984', 'My life in picture', 'Kevin Graham', 8, 2004, '001', false),
('9823-2403-0', 'DaVinci Code', 'Dan Brown', 1, 2003, '008', false),
('98234-029384', 'My ranch in Texas', 'George Bush', 1, 2005, '001', true),
('9823-98345', 'How to cook Italian food', 'Jamie Oliver', 2, 2005, '007', true),
('9823-98487', 'Optimising your business', 'Cleo Blair', 1, 2001, '002', false),
('988745-234', 'Tara Road', 'Maeve Binchy', 4, 2002, '008', false),
('993-004-00', 'My life in bits', 'John Smith', 1, 2001, '001', false),
('9987-0039882', 'Shooting History', 'Jon Snow', 1, 2003, '001', false);

-- insert sample data into reservations table
INSERT INTO reservedBooks (isbn, username, date) VALUES
('98234-029384', 'joecrotty', '11-Oct-2008'),
('9823-98345', 'tommy100', '11-Oct-2008');