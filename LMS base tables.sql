CREATE SCHEMA IF NOT EXISTS lms;
USE lms;

-- ===========================================================
-- 1. Lookup Tables (Reference / Domain Tables)
-- ===========================================================

-- User roles: instead of ENUM('borrower', 'lender', 'admin')
CREATE TABLE IF NOT EXISTS Role_Type (
    role_id   INT AUTO_INCREMENT PRIMARY KEY,
    role_name VARCHAR(50) UNIQUE NOT NULL
);

-- User account status
CREATE TABLE IF NOT EXISTS Account_Status (
    status_id   INT AUTO_INCREMENT PRIMARY KEY,
    status_name VARCHAR(50) UNIQUE NOT NULL
);

-- Loan status options
CREATE TABLE IF NOT EXISTS Loan_Status (
    loan_status_id   INT AUTO_INCREMENT PRIMARY KEY,
    status_name      VARCHAR(50) UNIQUE NOT NULL
);

-- Payment methods
CREATE TABLE IF NOT EXISTS Payment_Method (
    method_id   INT AUTO_INCREMENT PRIMARY KEY,
    method_name VARCHAR(50) UNIQUE NOT NULL
);

-- Schedule status
CREATE TABLE IF NOT EXISTS Schedule_Status (
    schedule_status_id INT AUTO_INCREMENT PRIMARY KEY,
    status_name        VARCHAR(50) UNIQUE NOT NULL
);

-- ===========================================================
-- 2. Base Tables
-- ===========================================================

CREATE TABLE IF NOT EXISTS Login_Info (
    user_id       INT AUTO_INCREMENT PRIMARY KEY,
    username      VARCHAR(50) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role_id       INT NOT NULL,
    status_id     INT NOT NULL,
    created_at    DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES Role_Type(role_id),
    FOREIGN KEY (status_id) REFERENCES Account_Status(status_id)
);

CREATE TABLE IF NOT EXISTS Borrower (
    borrower_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id     INT UNIQUE,
    first_name  VARCHAR(100) NOT NULL,
    last_name   VARCHAR(100) NOT NULL,
    contact_no  VARCHAR(20),
    email       VARCHAR(100),
    address     VARCHAR(255),
    FOREIGN KEY (user_id) REFERENCES Login_Info(user_id)
);

CREATE TABLE IF NOT EXISTS Lender (
    lender_id   INT AUTO_INCREMENT PRIMARY KEY,
    user_id     INT UNIQUE,
    first_name  VARCHAR(100) NOT NULL,
    last_name   VARCHAR(100) NOT NULL,
    contact_no  VARCHAR(20),
    email       VARCHAR(100),
    organization_name VARCHAR(100),
    FOREIGN KEY (user_id) REFERENCES Login_Info(user_id)
);

CREATE TABLE IF NOT EXISTS Loan (
    loan_id          INT AUTO_INCREMENT PRIMARY KEY,
    borrower_id      INT NOT NULL,
    lender_id        INT,
    loan_amount      DECIMAL(12,2) NOT NULL,
    interest_rate    DECIMAL(5,2) NOT NULL,
    term_months      INT NOT NULL,
    loan_status_id   INT DEFAULT 1,
    application_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    approval_date    DATETIME,
    FOREIGN KEY (borrower_id) REFERENCES Borrower(borrower_id),
    FOREIGN KEY (lender_id) REFERENCES Lender(lender_id),
    FOREIGN KEY (loan_status_id) REFERENCES Loan_Status(loan_status_id)
);

CREATE TABLE IF NOT EXISTS Payment (
    payment_id     INT AUTO_INCREMENT PRIMARY KEY,
    loan_id        INT NOT NULL,
    payment_date   DATETIME DEFAULT CURRENT_TIMESTAMP,
    amount_paid    DECIMAL(12,2) NOT NULL,
    method_id      INT NOT NULL,
    remarks        VARCHAR(255),
    FOREIGN KEY (loan_id) REFERENCES Loan(loan_id),
    FOREIGN KEY (method_id) REFERENCES Payment_Method(method_id)
);

CREATE TABLE IF NOT EXISTS Loan_Schedule (
    schedule_id         INT AUTO_INCREMENT PRIMARY KEY,
    loan_id             INT NOT NULL,
    due_date            DATE NOT NULL,
    due_amount          DECIMAL(12,2) NOT NULL,
    schedule_status_id  INT DEFAULT 1,
    FOREIGN KEY (loan_id) REFERENCES Loan(loan_id),
    FOREIGN KEY (schedule_status_id) REFERENCES Schedule_Status(schedule_status_id)
);

CREATE TABLE IF NOT EXISTS Transaction_Log (
    log_id      INT AUTO_INCREMENT PRIMARY KEY,
    user_id     INT,
    action      VARCHAR(100),
    action_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    details     TEXT,
    FOREIGN KEY (user_id) REFERENCES Login_Info(user_id)
);

-- Darius the NIGGA
-- Patrick the nigggs
