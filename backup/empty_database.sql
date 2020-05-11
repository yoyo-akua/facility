-- phpMyAdmin SQL Dump
-- version 4.7.7
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Aug 31, 2019 at 09:24 PM
-- Server version: 10.1.30-MariaDB
-- PHP Version: 7.2.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `facility`
--

-- --------------------------------------------------------

--
-- Table structure for table `anc`
--

CREATE TABLE `anc` (
  `ANC_ID` int(11) NOT NULL,
  `maternity_ID` int(11) NOT NULL,
  `FHt` int(6) NOT NULL,
  `fetal_heart` varchar(11) NOT NULL,
  `SP` tinyint(1) NOT NULL,
  `TT` tinyint(1) NOT NULL,
  `remarks` varchar(50) NOT NULL,
  `visitnumber` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `delivery`
--

CREATE TABLE `delivery` (
  `delivery_ID` int(11) NOT NULL,
  `del_category_ID` int(11) NOT NULL,
  `result` varchar(100) NOT NULL,
  `maternity_ID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `delivery_categories`
--

CREATE TABLE `delivery_categories` (
  `del_category_ID` int(11) NOT NULL,
  `category_name` varchar(50) NOT NULL,
  `outcomes` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `delivery_categories`
--

INSERT INTO `delivery_categories` (`del_category_ID`, `category_name`, `outcomes`) VALUES
(1, 'Presentation', 'cephalic,breech,longitudinal lie'),
(2, 'Partograph Use', 'yes,no'),
(3, 'Respiration', 'number(cpm)'),
(4, 'Cervical Dilatation', 'number(cm)'),
(5, 'Perineum', 'intact,torn'),
(6, 'Date + Time of Delivery', 'datetime'),
(7, 'Breathing + Crying at Birth', 'yes,no'),
(8, 'Pulse Rate', 'number(bpm)'),
(9, 'APGAR Score', 'number()'),
(10, 'Skin to Skin + Breastfeeding Within 30 min', 'yes,no'),
(11, 'Resuscitation Done', 'no,yes'),
(12, 'Axillary Temperature', 'number(°C)'),
(13, 'Birth Outcome', 'live birth,still birth'),
(14, 'Sex', ' ,male,female'),
(15, 'Weight', 'number(kg)'),
(16, 'Full Length', 'number(cm)'),
(17, 'Vitamin K Injection', 'yes,no'),
(18, 'Cord Care', 'yes,no'),
(19, 'Birth Abnormality', 'text'),
(20, 'Vitamin A', 'yes,no'),
(21, 'Time Of Oxytocin 10 units', 'datetime'),
(22, 'Estimated Blood Loss', 'number(mls)'),
(23, 'Mode of Delivery', 'SVD,CS'),
(24, 'State of Placenta', 'complete,incomplete'),
(25, 'any Complications', 'text'),
(26, 'Temperature at Discharge', 'number(°C)'),
(27, 'BP at Discharge', 'text(mmHg)'),
(28, 'Pulse at Discharge', 'number(bpm)'),
(29, 'Date + Time of Discharge', 'datetime'),
(30, 'Condition at Discharge', 'text'),
(31, 'Delivered by + Designation', 'text');

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `Department_ID` int(11) NOT NULL,
  `Department` varchar(20) NOT NULL,
  `IP` varchar(180) NOT NULL,
  `password` varchar(15) NOT NULL,
  `in_charge` varchar(30) NOT NULL,
  `notice` varchar(800) NOT NULL,
  `pages` varchar(1000) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`Department_ID`, `Department`, `IP`, `password`, `in_charge`, `notice`, `pages`) VALUES
(1, 'OPD', '', 'authorised', '', '', 'consulting_report.php\r\ncreate_patient.php\r\ncurrent_patients.php\r\ndelete_from_database.php\r\ndelete_from_protocol.php\r\ndisp_drug_protocol.php\r\ndisp_drugs.php\r\ndispensary.php\r\ndisp_patients.php\r\ndrug_report.php\r\nedit_patient.php\r\nhelp.php\r\nindex.php\r\nOPD.php\r\nOPD_report.php\r\norder_tests.php\r\npatient_drugs.php\r\npatient_protocol.php\r\npatient_visit.php\r\npatient_visit_pdf.php\r\nprescribe_drugs.php\r\nsearch_patient.php\r\nsettings.php\r\nsurgery.php\r\nvital_signs.php\r\n'),
(3, 'Laboratory', '', 'authorised', '', '', 'consulting_report.php\r\ncreate_patient.php\r\ncurrent_patients.php\r\ndelete_from_database.php\r\ndelete_from_protocol.php\r\nedit_patient.php\r\nhelp.php\r\nindex.php\r\nlab.php\r\nlab_patients.php\r\nnew_test.php\r\nOPD.php\r\nOPD_report.php\r\norder_tests.php\r\npatient_drugs.php\r\npatient_protocol.php\r\npatient_visit.php\r\npatient_visit_pdf.php\r\nsearch_patient.php\r\nsettings.php\r\nsetup.php\r\nsurgery.php\r\nvital_signs.php\r\n'),
(4, 'Maternity', '', 'authorised', '', '', 'anc.php\r\ncomplete_pregnancy.php\r\ncomplete_pregnancy_pdf.php\r\nconsulting_report.php\r\ncreate_patient.php\r\ncurrent_patients.php\r\ndelete_from_database.php\r\ndelete_from_protocol.php\r\ndelivery.php\r\ndisp_drug_protocol.php\r\ndisp_drugs.php\r\ndispensary.php\r\ndisp_patients.php\r\ndrug_report.php\r\nedit_patient.php\r\nhelp.php\r\nindex.php\r\nlab.php\r\nlab_patients.php\r\nmaternity_patients.php\r\nOPD.php\r\nOPD_report.php\r\norder_tests.php\r\npatient_drugs.php\r\npatient_protocol.php\r\npatient_visit.php\r\npatient_visit_pdf.php\r\nprescribe_drugs.php\r\nsearch_patient.php\r\nsettings.php\r\nsurgery.php\r\nvital_signs.php'),
(5, 'Consulting', '', 'authorised', '', '10,14,26,37,73', 'consulting_report.php\r\ncreate_patient.php\r\ncurrent_patients.php\r\ndelete_from_database.php\r\ndelete_from_protocol.php\r\ndisp_drug_protocol.php\r\ndisp_drugs.php\r\ndispensary.php\r\ndisp_patients.php\r\ndrug_report.php\r\nedit_patient.php\r\nempty_database.php\r\nindex.php\r\nlab.php\r\nlab_patients.php\r\nOPD.php\r\nOPD_report.php\r\norder_tests.php\r\npatient_drugs.php\r\npatient_protocol.php\r\npatient_visit.php\r\npatient_visit_pdf.php\r\nprescribe_drugs.php\r\nsearch_patient.php\r\nsettings.php\r\nsurgery.php\r\nvital_signs.php\r\n'),
(6, 'Dispensary', '', 'authorised', '', '', 'consulting_report.php\r\ncreate_patient.php\r\ncurrent_patients.php\r\ndelete_from_database.php\r\ndelete_from_protocol.php\r\ndisp_drug_protocol.php\r\ndisp_drugs.php\r\ndisp_patients.php\r\ndrug_report.php\r\nedit_patient.php\r\nhelp.php\r\nindex.php\r\nOPD.php\r\nOPD_report.php\r\norder_tests.php\r\npatient_drugs.php\r\npatient_protocol.php\r\npatient_visit.php\r\npatient_visit_pdf.php\r\nprescribe_drugs.php\r\nsearch_patient.php\r\nsettings.php\r\nsurgery.php\r\nvital_signs.php'),
(7, 'Store', '', 'authorised', '', '', 'consulting_report.php\r\ncreate_patient.php\r\ncurrent_patients.php\r\ndelete_from_database.php\r\ndelete_from_protocol.php\r\ndisp_drug_protocol.php\r\ndisp_drugs.php\r\ndispensary.php\r\ndisp_patients.php\r\ndrug_report.php\r\nedit_patient.php\r\nhelp.php\r\nindex.php\r\nnon_drug_protocol.php\r\nnon_drugs.php\r\nOPD.php\r\nOPD_report.php\r\norder_tests.php\r\npatient_drugs.php\r\npatient_protocol.php\r\npatient_visit.php\r\npatient_visit_pdf.php\r\nprescribe_drugs.php\r\nsearch_patient.php\r\nsettings.php\r\nstore.php\r\nstore_drug_protocol.php\r\nstore_drugs.php\r\nsurgery.php\r\nvital_signs.php\r\n');

-- --------------------------------------------------------

--
-- Table structure for table `diagnoses`
--

CREATE TABLE `diagnoses` (
  `Diagnosis_ID` int(11) NOT NULL,
  `DiagnosisName` varchar(200) NOT NULL,
  `DiagnosisClass` varchar(40) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `diagnoses`
--

INSERT INTO `diagnoses` (`Diagnosis_ID`, `DiagnosisName`, `DiagnosisClass`) VALUES
(1, 'AFP (Polio)', 'communicable immunizable'),
(2, 'Meningitis', 'communicable immunizable'),
(3, 'Neo-Natal Tetanus', 'communicable immunizable'),
(4, 'Pertussis (Whooping Cough)', 'communicable immunizable'),
(5, 'Diphteria', 'communicable immunizable'),
(6, 'Measles', 'communicable immunizable'),
(7, 'Yellow Fever', 'communicable immunizable'),
(8, 'Tetanus', 'communicable immunizable'),
(9, 'Tuberculosis', 'communicable immunizable'),
(10, 'Uncomplicated Malaria', 'communicable non-immunizable'),
(11, 'Severe Malaria', 'communicable non-immunizable'),
(12, 'Typhoid Fever', 'communicable non-immunizable'),
(13, 'Cholera', 'communicable non-immunizable'),
(14, 'Diarrhoea Diseases(Enteritis,Gastroenteritis,Diarrhoea)', 'communicable non-immunizable'),
(15, 'Viral Hepatitis', 'communicable non-immunizable'),
(16, 'Schistosomiasis(Bilhazia)', 'communicable non-immunizable'),
(17, 'Guinea Worm', 'communicable non-immunizable'),
(18, 'Onchocerciasis', 'communicable non-immunizable'),
(19, 'Buruli Ulcer', 'communicable non-immunizable'),
(20, 'Leprosy', 'communicable non-immunizable'),
(21, 'Infectious Yaws', 'communicable non-immunizable'),
(22, 'HIV/AIDS', 'communicable non-immunizable'),
(23, 'Mumps', 'communicable non-immunizable'),
(24, 'Helminthiasis', 'communicable non-immunizable'),
(25, 'Chicken Pox', 'communicable non-immunizable'),
(26, 'Upper Respiratory Tract Infections(URTI,LRTI,RTI)', 'communicable non-immunizable'),
(27, 'Pneumonia', 'communicable non-immunizable'),
(28, 'Septiceamia', 'communicable non-immunizable'),
(29, 'Malnutrition', 'non-communicable'),
(30, 'Obesity', 'non-communicable'),
(31, 'Anaemia', 'non-communicable'),
(32, 'Other Nutritional Diseases', 'non-communicable'),
(33, 'Hypertension', 'non-communicable'),
(34, 'Cardiac Diseases', 'non-communicable'),
(35, 'Stroke', 'non-communicable'),
(36, 'Diabetes Mellitus', 'non-communicable'),
(37, 'Rheumatism & Other Joint Pains(Arthritis,Lumbago,Musculoskeletal Pains, Arthralgia, Cephalgia, Spondylosis)', 'non-communicable'),
(38, 'Sickle Cell Disease', 'non-communicable'),
(39, 'Asthma', 'non-communicable'),
(40, 'Chronic Obstructed Pulmonary Disease (COPD)', 'non-communicable'),
(41, 'Breast Cancer', 'non-communicable'),
(42, 'Cervical Cancer', 'non-communicable'),
(43, 'Lymphoma', 'non-communicable'),
(44, 'Prostate Cancer', 'non-communicable'),
(45, 'Hepatocellular Carcinoma', 'non-communicable'),
(46, 'All Other Cancers', 'non-communicable'),
(47, 'Schizophrenia', 'mental health'),
(48, 'Acute Psychotic Disorder', 'mental health'),
(49, 'Mono Symptoms Delusion', 'mental health'),
(50, 'Depression', 'mental health'),
(51, 'Substance Abuse', 'mental health'),
(52, 'Epilepsy', 'mental health'),
(53, 'Autism', 'mental health'),
(54, 'Mental Retardation', 'mental health'),
(55, 'Attention Deficit Hyperactivity Disorder', 'mental health'),
(56, 'Conversion Disorder', 'mental health'),
(57, 'Post Traumatic Stress Syndrome', 'mental health'),
(58, 'Generalized Anxiety', 'mental health'),
(59, 'Other Anxiety Disorders', 'mental health'),
(60, 'Neurosis', 'mental health'),
(61, 'Acute Eye Infection/Conjunctivitis', 'specialized'),
(62, 'Cataract', 'specialized'),
(63, 'Trachoma', 'specialized'),
(64, 'Otitis Media', 'specialized'),
(65, 'Other Acute Ear Infection', 'specialized'),
(66, 'Dental Caries', 'specialized'),
(67, 'Dental Swellings', 'specialized'),
(68, 'Traumtic Conditions (Oral and Maxillofacial Region)', 'specialized'),
(69, 'Peridontal diseases', 'specialized'),
(70, 'Cerebral Palsy', 'specialized'),
(71, 'Liver Diseases', 'specialized'),
(72, 'Acute Urinary Tract Infection(UTI)', 'specialized'),
(73, 'Skin Diseases/Dermatitits', 'specialized'),
(74, 'Ulcer/Wounds/Lacerations/Circumcisions', 'specialized'),
(75, 'Kidney Related Diseases', 'specialized'),
(76, 'Other Oral Conditions', 'specialized'),
(78, 'Pregnancy Related Complications', 'obstetric & gynaecological'),
(79, 'Gonorrhoea', 'reproductive tract'),
(80, 'Genital Ulcer', 'reproductive tract'),
(81, 'Vaginal Discharge/Syphilis', 'reproductive tract'),
(82, 'Urethral Discharge', 'reproductive tract'),
(83, 'Other diseases of the Male reproductive system', 'reproductive tract'),
(84, 'Other diseases of the Female reproductive system', 'reproductive tract'),
(85, 'Transport injuries (Road Traffic Accidents)', 'injuries'),
(86, 'Home Injuries (Home Accidents and Injuries)', 'injuries'),
(87, 'Occupational Injuries', 'injuries'),
(88, 'Burns', 'injuries'),
(89, 'Poisoning (Occupational Poisoning)', 'injuries'),
(90, 'Dog Bite', 'injuries'),
(91, 'Human Bites', 'injuries'),
(92, 'Snake Bite', 'injuries'),
(93, 'Sexual Abuse', 'injuries'),
(94, 'Domestic Violence', 'injuries'),
(96, 'Other Animal Bites', 'injuries'),
(97, 'Pyrexia of unknown origin PUO (not Malaria)', 'others'),
(98, 'Brought in Dead', 'others'),
(99, 'All other Cases', 'others');

-- --------------------------------------------------------

--
-- Table structure for table `disp_drugs`
--

CREATE TABLE `disp_drugs` (
  `Disp_Drugs_ID` int(11) NOT NULL,
  `Drug_ID` int(11) NOT NULL,
  `Store_Drugs_ID` int(11) DEFAULT '0',
  `Prescribed` int(11) NOT NULL,
  `CountDate` datetime DEFAULT CURRENT_TIMESTAMP,
  `Counts` varchar(11) DEFAULT NULL,
  `dosage_recommendation` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `drugs`
--

CREATE TABLE `drugs` (
  `Drug_ID` int(11) NOT NULL,
  `Drugname` varchar(70) NOT NULL,
  `Unit_of_Issue` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `drugs`
--

INSERT INTO `drugs` (`Drug_ID`, `Drugname`, `Unit_of_Issue`) VALUES
(8, 'Adrenaline injection', 'ampoule'),
(9, 'Albendazole suspension 100mg', 'bottle'),
(10, 'Albendazole tablet 400mg', 'tablet'),
(11, 'Amoxicillin capsule 250mg', 'capsule'),
(12, 'Amoxicillin suspension 125mg', 'bottle'),
(13, 'Anti-Snake venum', 'vial'),
(14, 'Artemether Lumefantrine suspension 20/120mg', 'bottle'),
(15, 'Artemether Lumefantrine tablet 20/120mg (old data)', 'tablet'),
(16, 'Artesunate Amodiaquine tablet 25/67.5mg', 'tablet'),
(17, 'Artesunate Amodiaquine tablet 50/135mg', 'tablet'),
(18, 'Bendroflumethiazide tablet 2.5mg', 'tablet'),
(19, 'Amoxicillin capsule 500mg', 'capsule'),
(20, 'Artesunate Amodiaquine tablet 100/270mg (old data)', 'tablet'),
(21, 'Benzoic Acid + Salicylic Acid ointment', 'container'),
(22, 'Benzylpenicillin injection', 'vial'),
(23, 'Calamine cream', 'tube'),
(24, 'Cetrizine tablet 10mg', 'tablet'),
(25, 'Chloramphenicol ear drop', 'bottle'),
(26, 'Chloramphenicol eye drop', 'bottle'),
(27, 'Chloramphenicol eye ointment', 'tube'),
(28, 'Chlorhexidine cream', 'tube'),
(29, 'Chlorphenamine tablet 4mg', 'tablet'),
(30, 'Ciprofloxacin tablet 500mg', 'tablet'),
(31, 'Clotrimazole cream 1%', 'tube'),
(32, 'Clotrimazole pessary', 'pessary'),
(34, 'Cotrimoxazole suspension', 'bottle'),
(35, 'Cotrimoxazole tablet 480mg', 'tablet'),
(36, 'Diazepam injection', 'ampoule'),
(37, 'Dextrose infusion 5%', 'bag'),
(38, 'Dextrose infusion 10%', 'bag'),
(39, 'Dextrose infusion 50%', 'bag'),
(40, 'Diazepam tablet', 'tablet'),
(41, 'Diclofenac capsule', 'capsule'),
(42, 'Diclofenac injection', 'ampoule'),
(43, 'Diclofenac suppository 100mg', 'suppository'),
(44, 'Diclofenac tablet 50mg', 'tablet'),
(45, 'Diclofenac tablet 100mg', 'tablet'),
(46, 'Ephedrine nasal drops', 'bottle'),
(47, 'Erythromycin syrup 125mg', 'bottle'),
(48, 'Erythromycin tablet 250mg', 'tablet'),
(49, 'Ferrous Sulphate tablet 200mg', 'tablet'),
(50, 'Flucloxacillin capsule 250mg', 'capsule'),
(51, 'Flucloxacillin suspension 125mg', 'bottle'),
(52, 'Folic Acid tablet 5mg', 'tablet'),
(53, 'Gentamicin eye/ear drops', 'bottle'),
(54, 'Gentamicin injection', 'ampoule'),
(55, 'Hydrocortisone cream 1%', 'tube'),
(56, 'Hydrocortisone injection 100mg', 'bag'),
(57, 'Hyoscine Butylbromide injection', 'ampoule'),
(58, 'Hyoscine Butylbromide tablet 10mg', 'tablet'),
(59, 'Ibuprofen suspension', 'bottle'),
(60, 'Ibuprofen tablet 200mg', 'tablet'),
(61, 'Ibuprofen tablet 400mg', 'tablet'),
(62, 'Iron III Polymaltose capsule', 'capsule'),
(63, 'Iron III Polymaltose suspension', 'bottle'),
(64, 'Lidocaine injection', 'vial'),
(65, 'Lisinopril tablet', 'tablet'),
(66, 'Aluminium Hydroxide + Magnesium Trisillicate suspension', 'bottle'),
(67, 'Aluminium Hydroxide + Magnesium Trisillicate tablet', 'tablet'),
(68, 'Mebendazole suspension 100mg', 'bottle'),
(69, 'Mebendazole tablet', 'tablet'),
(70, 'Metronidazole suspension', 'bottle'),
(71, 'Metronidazole tablet 200mg', 'tablet'),
(72, 'Multivitamin syrup', 'bottle'),
(73, 'Multivitamin tablet', 'tablet'),
(74, 'Nifedipine tablet', 'tablet'),
(75, 'Oxytocin injection 5 units', 'vial'),
(76, 'Oral Rehydration Salt', 'sachet'),
(77, 'Paracetamol suppository 125mg', 'suppository'),
(78, 'Paracetamol suppository 250mg', 'suppository'),
(79, 'Paracetamol syrup 125mg', 'bottle'),
(80, 'Paracetamol tablet 500mg', 'tablet'),
(81, 'Promethazine Hydrochloride injection 25mg', 'ampoule'),
(82, 'Promethazine Hydrochloride syrup', 'bottle'),
(83, 'Quinine injection', 'ampoule'),
(84, 'Quinine tablet', 'tablet'),
(85, 'Salbutamol tablet', 'tablet'),
(86, 'Simple Linctus Adult syrup', 'bottle'),
(87, 'Simple Linctus Paediatric syrup', 'bottle'),
(88, 'Sodium Chloride infusion 0.9%', 'bag'),
(89, 'Sulfadoxine Pyrimethamine tablet 500/25mg', 'tablet'),
(90, 'Tetanus Diphteria vaccine', 'vial'),
(91, 'Tetracycline eye ointment', 'tube'),
(92, 'Water for Injection', 'ampoule'),
(93, 'Zinc tablet 10mg', 'tablet'),
(94, 'Zinc tablet 20mg', 'tablet'),
(95, 'Diclofenac gel', 'container'),
(96, 'Furosemide tablet 40mg', 'tablet'),
(97, 'Griseofulvin tablet 125mg', 'tablet'),
(98, 'Griseofulvin tablet 500mg', 'tablet'),
(99, 'Metronidazole infusion', 'bag'),
(100, 'Omeprazole tablet 20mg', 'tablet'),
(101, 'Ringers Lactate solution 500mls', 'bag'),
(102, 'Vitamin B complex tablet', 'tablet'),
(104, 'Artemether Lumefantrine tablet 20/120mg (12 per course)', 'tablet'),
(105, 'Artemether Lumefantrine tablet 20/120mg (6 per course)', 'tablet'),
(106, 'Artemether Lumefantrine tablet 20/120mg (24 per course)', 'tablet'),
(107, 'Artesunate Amodiaquine tablet 100/270mg (3 per course)', 'tablet'),
(108, 'Artesunate Amodiaquine tablet 100/270mg (6 per course)', 'tablet'),
(109, 'Triple Action cream', 'tube'),
(111, 'Ceftriaxone injection 1g', 'vial'),
(112, 'Dexamethazone', 'ampoule'),
(113, 'Magnesium Sulphate injection 50%', 'ampoule'),
(114, 'Dextrose 5% + Sodium Chloride 0.9% infusion', 'bag'),
(115, 'Vitamin K injection', 'vial'),
(116, 'Cetrizine syrup', 'bottle'),
(117, 'Chlorphenamine suspension', 'bottle');

-- --------------------------------------------------------

--
-- Table structure for table `lab`
--

CREATE TABLE `lab` (
  `lab_ID` int(11) NOT NULL,
  `protocol_ID` int(11) NOT NULL,
  `parameter_ID` int(11) NOT NULL,
  `test_results` varchar(1000) NOT NULL,
  `other_facility` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `maternity`
--

CREATE TABLE `maternity` (
  `maternity_ID` int(11) NOT NULL,
  `patient_ID` int(11) NOT NULL,
  `telephone` varchar(15) NOT NULL,
  `conception_date` date NOT NULL,
  `parity` varchar(10) NOT NULL,
  `height` decimal(4,1) NOT NULL,
  `ITN` tinyint(1) NOT NULL DEFAULT '0',
  `occupation` varchar(50) NOT NULL,
  `serial_number` varchar(11) NOT NULL,
  `reg_number` varchar(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `non_drugs`
--

CREATE TABLE `non_drugs` (
  `Non_Drug_ID` int(11) NOT NULL,
  `Non_Drugname` varchar(50) NOT NULL,
  `Receiving_Department` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `non_drugs`
--

INSERT INTO `non_drugs` (`Non_Drug_ID`, `Non_Drugname`, `Receiving_Department`) VALUES
(10, 'Alcohol Swabs', 'OPD'),
(11, 'Blood Grouping Reagents', 'Laboratory'),
(12, 'Blood Sample Bottles (Violet)', 'Laboratory'),
(13, 'Blood Sample Bottles (Yellow)', 'Laboratory'),
(14, 'Blue Cannulars', 'Dispensary'),
(15, 'Pink Cannulars', 'Dispensary'),
(16, 'Green Cannulars', 'Dispensary'),
(17, 'Yellow Cannulars', 'Dispensary'),
(18, 'Violet Cannulars', 'Dispensary'),
(19, 'Ash Cannulars', 'Dispensary'),
(20, 'Chlorine Solution', 'OPD'),
(21, 'Condoms', 'RCH'),
(22, 'Cotton Rolls', 'All'),
(23, 'Crepe Bandages', 'Dressing Room'),
(24, 'Depo Provera', 'RCH'),
(25, 'Dispensing Envelopes', 'Dispensary'),
(26, 'Disposable Gowns', 'Maternity'),
(27, 'Face Masks', 'All'),
(28, 'Folders', 'OPD'),
(29, 'Gauze Bandages', 'Dressing Room'),
(30, 'Gauze Rolls', 'Dressing Room'),
(31, 'Giving Sets', 'Dispensary'),
(32, 'Examination Gloves', 'Dispensary'),
(33, 'Steryle Gloves', 'Maternity'),
(34, 'Hep B Profile Test Strips', 'Laboratory'),
(35, 'Hep B Test Strips', 'Laboratory'),
(36, 'Hep C Test Strips', 'Laboratory'),
(37, 'HIV Test Kits', 'Laboratory'),
(38, 'Liquid Soap', 'All'),
(39, 'Methylated Spirit', 'All'),
(40, 'Micro-G', 'RCH'),
(41, 'Needles Only', 'Dispensary'),
(42, 'Norigynon', 'RCH'),
(43, 'OraQuick Strips HIV', 'Laboratory'),
(45, 'Pipets', 'Laboratory'),
(46, 'Plasters', 'Dressing Room'),
(47, 'Plumpy Nut', 'RCH'),
(48, 'Mosquito Nets', 'RCH'),
(49, 'Polythene Bags', 'Dispensary'),
(50, 'RDT Kits', 'Laboratory'),
(51, 'Sample Containers', 'Laboratory'),
(52, 'Savlon', 'Dressing Room'),
(53, 'Slides', 'Laboratory'),
(54, 'Surgical Blades', 'Dressing Room'),
(55, 'Chromic Sutures', 'Dressing Room'),
(56, 'Vycril Sutures', 'Dressing Room'),
(57, 'Syphilis Test Kits', 'Laboratory'),
(58, 'Solo Shots (0.5cc)', 'RCH'),
(59, 'Solo Shots (1cc)', 'RCH'),
(60, 'Syringes and Needles (2cc)', 'Laboratory'),
(61, 'Syringes and Needles (5cc)', 'Dispensary'),
(62, 'Syringes and Needles (10cc)', 'Dispensary'),
(63, 'Syringes and Needles (20cc)', 'Laboratory'),
(64, 'Urethtreal Catheters', 'Consulting Room'),
(65, 'Urin Bags', 'Consulting Room'),
(66, 'Urin Test Kits', 'Laboratory'),
(67, 'Wydal Test Reagents', 'Laboratory'),
(68, 'BP-Apparatus', 'OPD'),
(70, 'Cover glasses', 'Laboratory'),
(71, 'Prenancy Test Strips', 'Laboratory'),
(72, 'Female condom', 'RCH'),
(73, 'Microlut', 'RCH'),
(74, 'Cycle beads', 'r'),
(75, 'Blood lancet', 'Laboratory');

-- --------------------------------------------------------

--
-- Table structure for table `parameters`
--

CREATE TABLE `parameters` (
  `parameter_ID` int(11) NOT NULL,
  `test_ID` int(11) NOT NULL,
  `parameter_name` varchar(50) NOT NULL,
  `input_type` varchar(10) NOT NULL,
  `test_outcomes` varchar(500) NOT NULL,
  `units` varchar(300) NOT NULL,
  `reference_range` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `parameters`
--

INSERT INTO `parameters` (`parameter_ID`, `test_ID`, `parameter_name`, `input_type`, `test_outcomes`, `units`, `reference_range`) VALUES
(1, 1, 'RDT', 'radio', 'negative,positive', '', 'negative'),
(2, 1, 'BF for mps', 'radio', ' ,-,+,++,+++,++++', '', '-'),
(3, 2, '', 'number', '1.0-27.0', 'g/dl', 'men: 13.0-18.0 g/dl, women: 12.0-16.0 g/dl'),
(4, 4, '', 'number', '1.000-30.000', '/&#956l', '4.000-11.000/&#956l'),
(5, 5, '', 'radio', 'negative,positive', '', 'negative'),
(6, 6, '', 'select', 'A+,A-,B+,B-,AB+,AB-,O+,O-', '', ''),
(7, 7, 'first response', 'radio', 'negative,positive', '', 'negative'),
(8, 7, 'profiling', 'checkbox', 'HBsAg,HBsAb,HBeAg,HBcAg,HBcAb', '', ''),
(9, 8, '', 'radio', 'negative,positive', '', 'negative'),
(10, 9, '', 'radio', 'negative,positive', '', 'negative'),
(11, 10, 'O', 'select', '1/20,1/80,1/160,1/320', '', '1/20'),
(12, 10, 'H', 'select', '1/20,1/80,1/160,1/320', '', '1/20'),
(13, 11, 'First Response - Type 1', 'radio', 'negative,positive', '', 'negative'),
(14, 11, 'First Response - Type 2', 'radio', 'negative,positive', '', 'negative'),
(15, 11, 'Ora Quick', 'radio', 'negative,positive', '', 'negative'),
(16, 11, 'Overall - Type 1', 'radio', 'negative,positive,indeterminate', '', 'negative'),
(17, 11, 'Overall - Type 2', 'radio', 'negative,positive,indeterminate', '', 'negative'),
(18, 12, 'Macroscopy', 'textarea', '1000', '', ''),
(19, 12, 'Microscopy', 'textarea', '1000', '', ''),
(37, 19, 'RBS', 'number', '1.0-33.0', 'mmol/L', '4.0-11.0 mmol/L'),
(38, 20, '', 'radio', 'negative,positive', '', 'negative'),
(39, 21, '', 'radio', 'Normal,Partial Defect,Defect', '', 'Normal'),
(40, 22, 'Glucose', 'radio', '-,+ -,+,++,+++,++++', '', '-'),
(41, 22, 'Protein', 'radio', '-,+ -,+,++,+++,++++', '', '-'),
(42, 23, 'Appearance', 'radio', 'clear,hazy,cloudy,bloody,other', '', 'clear'),
(43, 23, 'Colour', 'radio', 'amber,straw,light,dark,colourless,other', '', 'amber'),
(44, 23, 'PH', 'number', '4.5-8.0', '', '6.0-7.5'),
(45, 23, 'Specific Gravity', 'number', '1.000-1.030', '', '1.005-1.025'),
(46, 23, 'Glucose', 'radio', '-,+ -,+,++,+++,++++', '', '-'),
(47, 23, 'Protein', 'radio', '-,+ -,+,++,+++,++++', '', '-'),
(48, 23, 'Blood', 'radio', '-,+ -,+,++,+++,++++', '', '-'),
(49, 23, 'Ketone', 'radio', '-,+ -,+,++,+++,++++', '', '-'),
(50, 23, 'Nitrite', 'radio', 'negative,positive', '', 'negative'),
(51, 23, 'Bilirubin', 'radio', '-,+ - ,+,++,+++,++++', '', '-'),
(52, 23, 'Leucocytes', 'radio', '-,+ -,+,++,+++,++++', '', '-'),
(53, 23, 'Urobilinogen', 'radio', 'Normal,+,++,+++', '', 'Normal'),
(54, 23, 'Pus Cells', 'number', '0-30', '/hpf', '< 5/hpf'),
(55, 23, 'Epithelial Cells', 'number', '0-30', '/lpf', '< 4/lpf'),
(56, 23, 'Red Blood Cells', 'number', '0-30', '/hpf', '< 2/hpf'),
(57, 23, 'Yeast', 'radio', '-,+,++,+++,++++', '', '-'),
(58, 23, 'Casts', 'text', '', '', ''),
(59, 23, 'Crystals', 'text', '', '', ''),
(60, 23, 'Others', 'textarea', '500', '', ''),
(61, 24, 'AST', 'number', '0.0-9999.9', 'IU/L', '8.0-40.0 IU/L'),
(62, 24, 'ALT', 'number', '0.0-9999.9', 'IU/L', '7.0-40.0 IU/L'),
(63, 24, 'GGT', 'number', '0.0-9999.9', 'IU/L', '9.0-40.0 IU/L'),
(64, 24, 'Total Protein', 'number', '0.0-9999.9', 'g/L', '63.0-79.0 g/l'),
(65, 24, 'Bilirubin Total', 'number', '0.0-9999.9', '&#956mol/l', '< 20.4 &#956mol/l'),
(66, 24, 'Bilirubin Direct', 'number', '0.0-9999.9', '&#956mol/l', '< 6.8 &#956mol/l'),
(67, 24, 'Albumin', 'number', '0.0-9999.9', 'g/l', '35.0-55.0 g/l'),
(68, 24, 'ALP', 'number', '0.0-9999.9', 'IU/L', '44.0-147.0 IU/L'),
(69, 25, 'Urea', 'number', '0.0-9999.9', 'mmol/l', '2.5-18.0'),
(70, 25, 'Creatinine', 'number', '0.0-9999.9', '&#956mol/l', '60.0-110.0 &#956mol/l'),
(71, 25, 'BUN', 'number', '0.0-100.0', '', '7.0-22.0'),
(72, 26, '', 'number', '0.0-9999.9', '&#956mol/l', 'men: 208.3-428.4 &#956mol/l, women: 154.7-357.0 &#956mol/l'),
(73, 27, '', 'number', '0.0-100.0', 'mmol/l', 'optimal: < 5.2 mmol/l, borderline: 5.3-6.2 mmol/l'),
(74, 28, '', 'textarea', '1000', '', ''),
(75, 29, '', 'textarea', '1000', '', ''),
(76, 30, '', 'textarea', '1000', '', ''),
(77, 31, '', 'textarea', '1000', '', ''),
(78, 32, '', 'textarea', '1000', '', ''),
(79, 33, '', 'textarea', '1000', '', ''),
(80, 34, 'Minutes', 'number', '0-100', '', '8-15'),
(81, 34, 'Seconds', 'number', '0-59', '', '0-59'),
(82, 1, 'BF for mps', 'number', '0-10000', 'parasites/l', '0'),
(83, 35, '', 'number', '0.00-2009', 'umol/L', '200 - 1200'),
(84, 19, 'FBS', 'number', '1.0-30.0', 'mmol/L', '4.0-7.0 mmol/L'),
(85, 23, 'Blood', 'checkbox', 'non-hemolized trace,non-hemolized +, non-hemolized ++', '', ''),
(86, 48, '', 'radio', 'negative,positive', '', 'negative'),
(87, 49, 'HBsAg', 'radio', 'negative,positive', '', ''),
(88, 49, 'HBsAb', 'radio', 'negative,positive', '', ''),
(89, 49, 'HBeAg', 'radio', 'negative,positive', '', ''),
(90, 49, 'HBeAb', 'radio', 'negative,positive', '', ''),
(91, 49, 'HBcAb', 'radio', 'negative,positive', '', ''),
(92, 49, 'Comments', 'textarea', '1000', '', ''),
(93, 50, 'Time Collected', 'text', '', '', ''),
(94, 50, 'Time Received', 'text', '', '', ''),
(95, 50, 'Time Analysed', 'text', '', '', ''),
(96, 50, 'Mode of Collection', 'radio', 'Coitus Interuptus,Masturbation', '', 'Coitus Interuptus'),
(97, 50, 'Appearance', 'text', '', '', 'Grey whitish'),
(98, 50, 'Volume', 'number', '0-1000', 'ml', '2-10ml'),
(99, 50, 'PH', 'number', '1.0-14.0', '', '7.0-8.5'),
(100, 50, 'WET EXAMINATION', 'checkbox', '.', '', ''),
(101, 50, '% Active (Progressively motile)', 'number', '0-100', '%', '>50%'),
(102, 50, '% Weak', 'number', '0-100', '%', '<50%'),
(103, 50, '% Immotile', 'number', '0-100', '%', '<20%'),
(104, 50, 'Pus Cells', 'number', '0-1000', '/hpf', '<5/hpf'),
(105, 50, 'Epithelial Cells', 'number', '0-1000', '/hpf', '<5/hpf'),
(106, 50, 'Red Blood Cells', 'number', '0-1000', '/hpf', '0/hpf'),
(107, 50, 'Immature Cells', 'number', '1-1000', '/hpf', '<20/hpf'),
(108, 50, 'Testicular Cells', 'number', '1-1000', '/hpf', '<5/hpf'),
(109, 50, 'Others', 'text', '', '', ''),
(110, 50, 'MORPHOLOGY', 'checkbox', '.', '', ''),
(111, 50, '% Normal', 'number', '0-100', '%', '>40%'),
(112, 50, '% Abnormal', 'number', '0-100', '%', '<40%'),
(113, 50, 'Viability', 'text', '', '', ''),
(114, 50, 'Sperm Count', 'number', '0-999', 'M spermatozoa', '>20 M spermatozoa'),
(115, 50, 'Sperm Concentration', 'number', '0', 'spermatozoa/ml', ''),
(116, 50, 'General Comment', 'textarea', '1000', '', ''),
(117, 52, 'WET EXAMINATION', 'checkbox', '.', '', '.'),
(118, 52, 'Pus Cells', 'number', '0-30', '/hpf', ''),
(119, 52, 'Epithelial cells', 'number', '0-30', '/hpf', ''),
(120, 52, 'Red Blood cells', 'number', '0-30', '/hpf', ''),
(121, 52, 'Yeast', 'text', '', '', ''),
(122, 52, 'Trachomonas Vaginalis', 'text', '', '', ''),
(123, 52, 'GRAM STAIN', 'checkbox', '.', '', ''),
(124, 52, 'Gram resuts', 'text', '', '', ''),
(125, 52, 'Isolate', 'text', '', '', ''),
(126, 52, 'ANTIBIOGRAM', 'checkbox', '.', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `patient`
--

CREATE TABLE `patient` (
  `patient_ID` int(11) NOT NULL,
  `Name` varchar(100) NOT NULL,
  `OPD` varchar(7) NOT NULL,
  `NHIS` varchar(8) DEFAULT NULL,
  `Birthdate` date NOT NULL,
  `NHISofMother` tinyint(1) DEFAULT '0',
  `Sex` varchar(6) DEFAULT NULL,
  `Locality` varchar(50) DEFAULT NULL,
  `blood_group` varchar(3) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `protocol`
--

CREATE TABLE `protocol` (
  `protocol_ID` int(11) NOT NULL,
  `patient_ID` int(11) NOT NULL,
  `VisitDate` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `new_p` tinyint(1) NOT NULL DEFAULT '0',
  `disp_drugs_IDs` varchar(500) NOT NULL,
  `completed` tinyint(1) NOT NULL DEFAULT '0',
  `Diagnosis_IDs` varchar(500) NOT NULL,
  `attendant` varchar(50) NOT NULL DEFAULT '',
  `referral` varchar(300) NOT NULL,
  `BP` varchar(7) NOT NULL,
  `weight` decimal(4,1) NOT NULL DEFAULT '0.0',
  `pulse` int(11) NOT NULL DEFAULT '0',
  `temperature` decimal(3,1) NOT NULL DEFAULT '0.0',
  `MUAC` decimal(3,1) NOT NULL DEFAULT '0.0',
  `ANC_ID` varchar(50) NOT NULL,
  `pregnant` tinyint(4) NOT NULL DEFAULT '0',
  `surgery` varchar(60) NOT NULL,
  `protect` tinyint(1) NOT NULL DEFAULT '0',
  `CCC` varchar(5) NOT NULL DEFAULT '',
  `Expired` tinyint(1) NOT NULL DEFAULT '0',
  `PNC` tinyint(1) NOT NULL DEFAULT '0',
  `entered` tinyint(1) NOT NULL DEFAULT '0',
  `onlylab` varchar(1) NOT NULL DEFAULT '0',
  `labdone` tinyint(1) NOT NULL,
  `charge` decimal(5,2) NOT NULL,
  `lab_number` varchar(11) NOT NULL,
  `remarks` varchar(1000) NOT NULL,
  `delivery` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `store_drugs`
--

CREATE TABLE `store_drugs` (
  `Store_Drugs_ID` int(11) NOT NULL,
  `Drug_ID` int(11) NOT NULL,
  `Storedate` date NOT NULL,
  `Particulars` varchar(100) NOT NULL,
  `Received` int(11) NOT NULL,
  `Issued` int(11) NOT NULL,
  `Initials` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `store_non_drugs`
--

CREATE TABLE `store_non_drugs` (
  `Store_Non_Drugs_ID` int(11) NOT NULL,
  `Non_Drug_ID` int(11) NOT NULL,
  `Storedate` date NOT NULL,
  `Particulars` varchar(100) NOT NULL,
  `Received` int(11) NOT NULL,
  `Issued` int(11) NOT NULL,
  `Initials` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tests`
--

CREATE TABLE `tests` (
  `test_ID` int(11) NOT NULL,
  `test_name` varchar(50) NOT NULL,
  `frequency` varchar(10) NOT NULL,
  `sex_limit` varchar(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tests`
--

INSERT INTO `tests` (`test_ID`, `test_name`, `frequency`, `sex_limit`) VALUES
(1, 'Malaria', 'frequent', ''),
(2, 'HB', 'normal', ''),
(4, 'WBC', 'rare', ''),
(5, 'Sickling', 'rare', ''),
(6, 'Blood Group', 'rare', ''),
(8, 'Hepatitis C', 'rare', ''),
(9, 'VDRL', 'normal', ''),
(10, 'Widal', 'normal', ''),
(11, 'HIV', 'normal', ''),
(12, 'Stool', 'rare', ''),
(19, 'Blood Sugar', 'rare', ''),
(20, 'UPT', 'frequent', 'female'),
(21, 'G6PD', 'normal', ''),
(22, 'Urine Glucose+Protein', 'frequent', ''),
(23, 'Urine R/E', 'frequent', ''),
(24, 'LFT', 'rare', ''),
(25, 'KFT', 'rare', ''),
(26, 'Serum Uric Acid', 'rare', ''),
(27, 'Total Cholesterol', 'rare', ''),
(29, 'Skin Snip', 'rare', ''),
(30, 'Blood Film Comments', 'rare', ''),
(31, 'OGTT', 'rare', ''),
(32, 'Sputum for AFB', 'rare', ''),
(34, 'Clotting Time', 'rare', ''),
(35, 'Prostate Specific Antigen ', 'rare', 'male'),
(48, 'HBV Screening ', 'normal', ''),
(49, 'HBV Profile Test', 'rare', ''),
(50, 'Semen Analysis', 'rare', ''),
(52, 'High Vaginal Swab', 'rare', '');

-- --------------------------------------------------------

--
-- Table structure for table `token`
--

CREATE TABLE `token` (
  `token` decimal(30,4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `token`
--

INSERT INTO `token` (`token`) VALUES
('1563442662.3304'),
('1563442736.6863'),
('1563442719.4938'),
('1563442841.4331'),
('1563442793.4020'),
('1563442946.4577'),
('1563443100.6550'),
('1563442986.1283'),
('1563443124.9739'),
('1563443187.6060'),
('1563443171.6842'),
('1563442999.9631'),
('1563442951.1031'),
('1563442792.2794'),
('1563443042.2945'),
('1563443213.1451'),
('1563443080.4431'),
('1563443698.3653'),
('1563443724.5670'),
('1563443114.2488'),
('1563443863.1299'),
('1563443980.2315'),
('1563443997.1221'),
('1563443149.1975'),
('1563443902.8569'),
('1563444078.8111'),
('1563444030.3069'),
('1563444144.2830'),
('1563444114.7806'),
('1563443966.0966'),
('1563444158.1846'),
('1563444225.3961'),
('1563444182.0556'),
('1563444245.6539'),
('1563444012.4913'),
('1563444197.0701'),
('1563444274.2233'),
('1563444348.5752'),
('1563444409.3949'),
('1563444296.0475'),
('1563444441.6315'),
('1563444653.3057'),
('1563444535.0280'),
('1563444819.4436'),
('1563444963.6660'),
('1563444493.2577'),
('1563444467.2925'),
('1563444916.5180'),
('1563444782.3850'),
('1563444782.3850'),
('1563445513.1080'),
('1563445391.4631'),
('1563445597.9469'),
('1563445561.5397'),
('1563445083.6926'),
('1563445541.4244'),
('1563445662.4635'),
('1563445713.1939'),
('1563445775.8205'),
('1563446034.7815'),
('1563445538.7780'),
('1563445996.6376'),
('1563446222.6905'),
('1563446462.0908'),
('1563446534.8445'),
('1563446693.5781'),
('1563446710.9569'),
('1563446515.8063'),
('1563446192.5295'),
('1563446757.6417'),
('1563446548.4461'),
('1563447494.6500'),
('1563447652.4978'),
('1563446818.0924'),
('1563447673.3829'),
('1563446543.8208'),
('1563447695.2111'),
('1563447711.3299'),
('1563447805.4786'),
('1563447949.5117'),
('1563448025.1482'),
('1563447966.7744'),
('1563448057.0953'),
('1563447440.2826'),
('1563447803.7878'),
('1563448446.6621'),
('1563448577.2063'),
('1563448651.0560'),
('1563448159.3298'),
('1563449040.6639'),
('1563448896.3085'),
('1563449074.9457'),
('1563448772.5893'),
('1563447882.5423'),
('1563448932.9623'),
('1563448924.9406'),
('1563449151.3451'),
('1563449191.2169'),
('1563449111.9043'),
('1563449255.8041'),
('1563449366.0138'),
('1563449322.7678'),
('1563449448.0845'),
('1563449289.5763'),
('1563449398.5231'),
('1563449207.5619'),
('1563449520.6030'),
('1563449472.5658'),
('1563449546.2845'),
('1563449501.8457'),
('1563450152.4494'),
('1563451164.2034'),
('1563451306.3079'),
('1563449761.8921'),
('1563449455.7649'),
('1563451075.3833'),
('1563451514.3086'),
('1563449419.2367'),
('1563451323.5414'),
('1563451419.2524'),
('1563451561.9979'),
('1563452047.3096'),
('1563449173.1425'),
('1563451370.4053'),
('1563451968.0002'),
('1563451386.3700'),
('1563449342.7584'),
('1563451858.4362'),
('1563451994.2175'),
('1563452160.9443'),
('1563452308.4471'),
('1563452185.3466'),
('1563452323.3819'),
('1563452285.6007'),
('1563452080.5910'),
('1563452625.4780'),
('1563452236.2921'),
('1563452726.5672'),
('1563452612.5114'),
('1563452850.3537'),
('1563452768.8839'),
('1563452701.2926'),
('1563452831.2494'),
('1563453453.1136'),
('1563453063.4895'),
('1563453192.5141'),
('1563452905.5862'),
('1563452799.7656'),
('1563453518.5035'),
('1563451191.5611'),
('1563453010.6883'),
('1563453502.1736'),
('1563455054.6519'),
('1563453118.8364'),
('1563455547.9144'),
('1563455913.7945'),
('1563456243.2157'),
('1563456134.1578'),
('1563455341.0764'),
('1563453702.3577'),
('1563457982.1726'),
('1563456081.5219'),
('1563458054.2132'),
('1563456382.1718'),
('1563458031.0662'),
('1563458014.2757'),
('1563458000.4511'),
('1563458125.5799'),
('1563455441.3587'),
('1563458280.4625'),
('1563458097.8860'),
('1563458350.0976'),
('1563458242.5765'),
('1563459557.2941'),
('1563458298.4895'),
('1563458366.7345'),
('1563459829.2196'),
('1563459847.9569'),
('1563459817.9014'),
('1563459876.0847'),
('1563459538.6576'),
('1563459889.9312'),
('1563460111.8230'),
('1563460098.3316'),
('1563460538.6898'),
('1563460119.9132'),
('1563461074.9347'),
('1563463713.4005'),
('1563460129.3482'),
('1563464556.5814'),
('1563463786.4138'),
('1563460981.6760'),
('1563464582.3051'),
('1563464631.8227'),
('1563460993.6173'),
('1563461020.0198'),
('1563465179.8747'),
('1563464661.2615'),
('1563465214.8311'),
('1563465330.2955'),
('1563785702.3082'),
('1563465350.3160'),
('1563464791.5780'),
('1563870323.7020'),
('1564125408.9581'),
('1563465239.3664'),
('1564125177.2448'),
('1564129885.8008'),
('1564132320.9223'),
('1564128256.8220'),
('1564220219.5706'),
('1564132350.4605'),
('1564220070.4401'),
('1564220725.4171'),
('1564125384.3851'),
('1564128185.9183'),
('1564668051.2419'),
('1564220777.9917'),
('1563189101.5212'),
('1563189508.7719'),
('1563190261.0992'),
('1563189832.6478'),
('1563190818.9817'),
('1563190957.9377'),
('1563191358.6278'),
('1563191755.0196'),
('1563191661.8349'),
('1563190642.6539'),
('1563192262.2416'),
('1563192702.9417'),
('1563192796.4327'),
('1563192056.5902'),
('1564220788.9275'),
('1563193622.8099'),
('1563193570.3615'),
('1563193661.5564'),
('1563193757.7203'),
('1563193797.9179'),
('1563194492.1843'),
('1563193816.3044'),
('1563194047.0438'),
('1563194547.6817'),
('1563194680.8018'),
('1563194779.7337'),
('1563193680.2918'),
('1563193909.1107'),
('1563194807.0993'),
('1563195045.9616'),
('1563196047.9098'),
('1563196677.3425'),
('1563195205.1958'),
('1563195695.7844'),
('1563196259.2045'),
('1563195538.9084'),
('1563197451.7480'),
('1563198208.8977'),
('1563197682.2182'),
('1563197570.4338'),
('1563198413.5974'),
('1563198038.5097'),
('1563196810.6754'),
('1563199879.2479'),
('1563197737.7597'),
('1563200063.9243'),
('1563200168.3246'),
('1563198597.7099'),
('1563199275.6010'),
('1563198768.7201'),
('1563199023.7847'),
('1563199703.7377'),
('1563206536.9041'),
('1563200442.9638'),
('1563200941.7230'),
('1563206879.1258'),
('1563200515.9767'),
('1563207306.7490'),
('1563207161.1365'),
('1563207240.3899'),
('1563207495.2696'),
('1563207023.8116'),
('1563264635.9342'),
('1563207682.6095'),
('1563267480.4080'),
('1563267324.4518'),
('1563267641.6862'),
('1563267840.3951'),
('1563267399.2083'),
('1563269092.7063'),
('1563268600.9782'),
('1563268836.5778'),
('1563270039.2978'),
('1563268627.0819'),
('1563270622.0654'),
('1563270267.5660'),
('1563270799.3327'),
('1563271006.5680'),
('1563271574.2679'),
('1563271199.3363'),
('1563269692.1576'),
('1563272105.0106'),
('1563271814.3001'),
('1563273639.7328'),
('1563271792.6928'),
('1563272420.6136'),
('1563269464.9015'),
('1563272623.7739'),
('1563274137.8655'),
('1563273002.8593'),
('1563274363.4776'),
('1563272208.4659'),
('1563274508.4530'),
('1563274763.1183'),
('1563275113.9581'),
('1563275448.6896'),
('1563275869.2460'),
('1563276165.2823'),
('1563276341.9260'),
('1563276655.7896'),
('1563278126.8257'),
('1563278896.4040'),
('1563278478.0924'),
('1563277201.6829'),
('1563279110.6503'),
('1563280050.3569'),
('1563279588.8335'),
('1563279357.9818'),
('1563279778.7839'),
('1563281143.6778'),
('1563280924.9699'),
('1563281469.6223'),
('1563280175.9961'),
('1563280554.0576'),
('1563280420.3214'),
('1563281462.3967'),
('1563281062.6776'),
('1563280350.4855'),
('1563281729.6282'),
('1563281626.9008'),
('1563282533.4533'),
('1563283027.4309'),
('1563281606.5258'),
('1563281825.0477'),
('1563283215.2335'),
('1563282851.4035'),
('1563283886.7724'),
('1563283906.2976'),
('1563282137.9842'),
('1563284460.0101'),
('1563284478.3921'),
('1563284656.2092'),
('1563284673.4861'),
('1563284785.8374'),
('1563286337.2584'),
('1563287079.3073'),
('1563286332.2906'),
('1563286495.3221'),
('1563284815.5928'),
('1563288683.6910'),
('1563284801.7637'),
('1563288672.2360'),
('1563290289.8609'),
('1563288730.1478'),
('1563294425.7015'),
('1563291668.7016'),
('1563353543.8504'),
('1563353606.4176'),
('1563353898.4616'),
('1563288711.0993'),
('1563353950.0821'),
('1563353947.6176'),
('1563354589.6860'),
('1563354247.4708'),
('1563354450.2484'),
('1563354104.6713'),
('1563294510.9254'),
('1563354652.9994'),
('1563354894.9189'),
('1563355458.4439'),
('1563355760.7661'),
('1563354661.4479'),
('1563354717.2410'),
('1563355369.5564'),
('1563355753.0466'),
('1563357095.1560'),
('1563355755.8301'),
('1563355989.2736'),
('1563357576.2318'),
('1563354550.4382'),
('1563357230.8789'),
('1563357702.3367'),
('1563357764.6497'),
('1563356894.8686'),
('1563358029.0493'),
('1563358262.9355'),
('1563357590.4657'),
('1563357703.4616'),
('1563358872.9404'),
('1563358161.5145'),
('1563358007.5460'),
('1563358658.7982'),
('1563358399.5337'),
('1563358904.0616'),
('1563359091.2487'),
('1563359353.6007'),
('1563359214.8940'),
('1563359510.7856'),
('1563359460.0012'),
('1563358867.4021'),
('1563359376.8096'),
('1563359857.2080'),
('1563359619.8425'),
('1563360316.7858'),
('1563359857.7313'),
('1563360664.8502'),
('1563360955.4538'),
('1563361189.5457'),
('1563361157.5911'),
('1563361535.1465'),
('1563361674.2018'),
('1563361183.4024'),
('1563361175.9193'),
('1563363847.2458'),
('1563366192.0592'),
('1563365601.2696'),
('1563362209.4501'),
('1563362487.2620'),
('1563367051.0425'),
('1563366199.6920'),
('1563367597.2045'),
('1563366484.7737'),
('1563367118.9374'),
('1563372836.2459'),
('1563372918.6840'),
('1563373570.6649'),
('1563373593.8602'),
('1563373610.6622'),
('1563373628.5771'),
('1563373768.4016'),
('1563373653.7061'),
('1563373250.8712'),
('1563378625.7194'),
('1563377992.5222'),
('1563379040.9899'),
('1563379124.1333'),
('1563367233.2180'),
('1563378004.9323'),
('1563379519.9787'),
('1563379048.8321'),
('1563381269.7418'),
('1563379503.3320'),
('1563382149.0459'),
('1563382458.9257'),
('1563382402.0808'),
('1563382171.3205'),
('1563379139.7015'),
('1563384981.3372'),
('1563385050.7619'),
('1563438499.7191'),
('1563438364.8518'),
('1563438562.4380'),
('1563382206.7017'),
('1563438514.8781'),
('1563440571.7823'),
('1563440901.1019'),
('1563438364.2421'),
('1563440777.2975'),
('1563441048.5590'),
('1563441527.1815'),
('1563441455.0597'),
('1563441557.6467'),
('1563441330.2693'),
('1563441851.8933'),
('1563441347.1666'),
('1563441617.1117'),
('1563441896.2501'),
('1563441863.3082'),
('1563441828.2374'),
('1563441868.2316'),
('1563441930.3009'),
('1563441744.6821'),
('1563441966.1661'),
('1563442021.5144'),
('1563442030.3732'),
('1563441985.2664'),
('1563442124.6899'),
('1563442161.8084'),
('1563442014.1463'),
('1563442143.3226'),
('1563441981.2749'),
('1563442230.0993'),
('1563442225.8580'),
('1563442174.8236'),
('1563442221.5081'),
('1563442264.5257'),
('1563442279.2737'),
('1563442317.3486'),
('1563442354.5895'),
('1563442249.4684'),
('1563442404.2378'),
('1563442358.3024'),
('1563442279.8293'),
('1563442435.0053'),
('1563442419.3564'),
('1563442332.6606'),
('1563442360.9816'),
('1563442467.5854'),
('1563442643.1867'),
('1563442607.0886'),
('1563442689.9368'),
('1563442694.6125'),
('1563442427.0577'),
('1563442715.1926');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `anc`
--
ALTER TABLE `anc`
  ADD PRIMARY KEY (`ANC_ID`);

--
-- Indexes for table `delivery`
--
ALTER TABLE `delivery`
  ADD PRIMARY KEY (`delivery_ID`);

--
-- Indexes for table `delivery_categories`
--
ALTER TABLE `delivery_categories`
  ADD PRIMARY KEY (`del_category_ID`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`Department_ID`);

--
-- Indexes for table `diagnoses`
--
ALTER TABLE `diagnoses`
  ADD PRIMARY KEY (`Diagnosis_ID`);

--
-- Indexes for table `disp_drugs`
--
ALTER TABLE `disp_drugs`
  ADD PRIMARY KEY (`Disp_Drugs_ID`);

--
-- Indexes for table `drugs`
--
ALTER TABLE `drugs`
  ADD PRIMARY KEY (`Drug_ID`);

--
-- Indexes for table `lab`
--
ALTER TABLE `lab`
  ADD PRIMARY KEY (`lab_ID`);

--
-- Indexes for table `maternity`
--
ALTER TABLE `maternity`
  ADD PRIMARY KEY (`maternity_ID`);

--
-- Indexes for table `non_drugs`
--
ALTER TABLE `non_drugs`
  ADD PRIMARY KEY (`Non_Drug_ID`);

--
-- Indexes for table `parameters`
--
ALTER TABLE `parameters`
  ADD PRIMARY KEY (`parameter_ID`);

--
-- Indexes for table `patient`
--
ALTER TABLE `patient`
  ADD PRIMARY KEY (`patient_ID`),
  ADD KEY `NHIS` (`NHIS`);

--
-- Indexes for table `protocol`
--
ALTER TABLE `protocol`
  ADD PRIMARY KEY (`protocol_ID`);

--
-- Indexes for table `store_drugs`
--
ALTER TABLE `store_drugs`
  ADD PRIMARY KEY (`Store_Drugs_ID`);

--
-- Indexes for table `store_non_drugs`
--
ALTER TABLE `store_non_drugs`
  ADD PRIMARY KEY (`Store_Non_Drugs_ID`);

--
-- Indexes for table `tests`
--
ALTER TABLE `tests`
  ADD PRIMARY KEY (`test_ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `anc`
--
ALTER TABLE `anc`
  MODIFY `ANC_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=101;

--
-- AUTO_INCREMENT for table `delivery`
--
ALTER TABLE `delivery`
  MODIFY `delivery_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=257;

--
-- AUTO_INCREMENT for table `delivery_categories`
--
ALTER TABLE `delivery_categories`
  MODIFY `del_category_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `Department_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `diagnoses`
--
ALTER TABLE `diagnoses`
  MODIFY `Diagnosis_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=134;

--
-- AUTO_INCREMENT for table `disp_drugs`
--
ALTER TABLE `disp_drugs`
  MODIFY `Disp_Drugs_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13401;

--
-- AUTO_INCREMENT for table `drugs`
--
ALTER TABLE `drugs`
  MODIFY `Drug_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=118;

--
-- AUTO_INCREMENT for table `lab`
--
ALTER TABLE `lab`
  MODIFY `lab_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5247;

--
-- AUTO_INCREMENT for table `maternity`
--
ALTER TABLE `maternity`
  MODIFY `maternity_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `non_drugs`
--
ALTER TABLE `non_drugs`
  MODIFY `Non_Drug_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=76;

--
-- AUTO_INCREMENT for table `parameters`
--
ALTER TABLE `parameters`
  MODIFY `parameter_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=127;

--
-- AUTO_INCREMENT for table `patient`
--
ALTER TABLE `patient`
  MODIFY `patient_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21661;

--
-- AUTO_INCREMENT for table `protocol`
--
ALTER TABLE `protocol`
  MODIFY `protocol_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4672;

--
-- AUTO_INCREMENT for table `store_drugs`
--
ALTER TABLE `store_drugs`
  MODIFY `Store_Drugs_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3900;

--
-- AUTO_INCREMENT for table `store_non_drugs`
--
ALTER TABLE `store_non_drugs`
  MODIFY `Store_Non_Drugs_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=951;

--
-- AUTO_INCREMENT for table `tests`
--
ALTER TABLE `tests`
  MODIFY `test_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
