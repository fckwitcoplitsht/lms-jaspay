-- Step 1: Lookup / Reference Data
-- ===========================
-- 1. Lookup Tables
-- ===========================
INSERT INTO Role_Type (role_name)
VALUES 
('borrower'),
('lender'),
('admin');

INSERT INTO Account_Status (status_name)
VALUES 
('active'),
('inactive');

INSERT INTO Loan_Status (status_name)
VALUES 
('pending'),
('approved'),
('rejected'),
('active'),
('completed');

INSERT INTO Payment_Method (method_name)
VALUES 
('cash'),
('bank_transfer'),
('online');

INSERT INTO Schedule_Status (status_name)
VALUES 
('pending'),
('paid'),
('overdue');

-- Step 2: User Accounts (Login_Info)
-- ===========================
-- 2. Login_Info (Users)
-- ===========================
INSERT INTO Login_Info (username, password_hash, role_id, status_id)
VALUES
('alice_borrower', 'hash123', 1, 1), -- Borrower
('bob_borrower', 'hash234', 1, 1),   -- Borrower
('lender_john', 'hash345', 2, 1),    -- Lender
('lender_mary', 'hash456', 2, 1),    -- Lender
('admin_tina', 'hash789', 3, 1);     -- Admin

-- Borrowers
-- ===========================
-- 3. Borrowers
-- ===========================
INSERT INTO Borrower (user_id, first_name, last_name, contact_no, email, address)
VALUES
(1, 'Alice', 'Lopez', '09171234567', 'alice@example.com', 'Quezon City'),
(2, 'Bob', 'Reyes', '09182345678', 'bob@example.com', 'Cebu City');

-- Lenders
-- ===========================
-- 4. Lenders
-- ===========================
INSERT INTO Lender (user_id, first_name, last_name, contact_no, email, organization_name)
VALUES
(3, 'John', 'Santos', '09193456789', 'john.lender@example.com', 'MicroCredit PH'),
(4, 'Mary', 'Tan', '09204567890', 'mary.tan@example.com', 'EasyFinance Inc.');

-- Loans
-- ===========================
-- 5. Loans
-- ===========================
INSERT INTO Loan (borrower_id, lender_id, loan_amount, interest_rate, term_months, loan_status_id, application_date, approval_date)
VALUES
(1, 1, 50000.00, 5.5, 12, 4, '2025-01-05', '2025-01-10'),  -- active
(2, 2, 30000.00, 6.0, 6, 2, '2025-02-01', '2025-02-05'),   -- approved but not yet active
(1, 1, 100000.00, 7.0, 24, 1, '2025-03-01', NULL),         -- pending
(2, 2, 20000.00, 6.0, 6, 3, '2025-04-01', NULL);           -- rejected

-- Payments
-- ===========================
-- 6. Payments
-- ===========================
INSERT INTO Payment (loan_id, payment_date, amount_paid, method_id, remarks)
VALUES
(1, '2025-02-05', 4500.00, 3, 'First installment'),
(1, '2025-03-05', 4500.00, 3, 'Second installment'),
(2, '2025-03-01', 5000.00, 2, 'Downpayment'),
(2, '2025-04-01', 5000.00, 1, 'Monthly installment');

-- Loan Schedules
-- ===========================
-- 7. Loan_Schedule
-- ===========================
INSERT INTO Loan_Schedule (loan_id, due_date, due_amount, schedule_status_id)
VALUES
-- Loan 1 (Alice)
(1, '2025-02-05', 4500.00, 2),  -- paid
(1, '2025-03-05', 4500.00, 2),  -- paid
(1, '2025-04-05', 4500.00, 1),  -- pending
-- Loan 2 (Bob)
(2, '2025-03-01', 5000.00, 2),
(2, '2025-04-01', 5000.00, 2),
(2, '2025-05-01', 5000.00, 1);

-- Transaction Logs
-- ===========================
-- 8. Transaction_Log
-- ===========================
INSERT INTO Transaction_Log (user_id, action, details)
VALUES
(1, 'Loan Application', 'Borrower Alice applied for a ₱50,000 loan.'),
(3, 'Loan Approval', 'Lender John approved Alice’s loan #1.'),
(1, 'Payment Made', 'Alice paid ₱4,500 via online transfer.'),
(2, 'Loan Application', 'Borrower Bob applied for ₱30,000 loan.'),
(4, 'Loan Review', 'Lender Mary approved Bob’s loan #2.'),
(5, 'Admin Review', 'Admin Tina generated monthly report.');