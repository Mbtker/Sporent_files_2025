-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 08, 2022 at 06:17 AM
-- Server version: 10.4.20-MariaDB
-- PHP Version: 7.4.21

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_spornt`
--

-- --------------------------------------------------------

--
-- Table structure for table `advertisingagencies`
--

CREATE TABLE `advertisingagencies` (
  `Id` int(11) NOT NULL,
  `Logo` varchar(255) DEFAULT NULL,
  `Name` varchar(255) DEFAULT NULL,
  `CR` varchar(255) DEFAULT NULL,
  `QRcode` varchar(255) DEFAULT NULL,
  `Email` varchar(255) DEFAULT NULL,
  `Phone` varchar(255) DEFAULT NULL,
  `CityName` varchar(255) DEFAULT NULL,
  `Location` varchar(255) DEFAULT NULL,
  `E-PaymentInfoId` int(11) DEFAULT NULL,
  `CreateDate` datetime DEFAULT NULL,
  `Status` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `attachments`
--

CREATE TABLE `attachments` (
  `Id` int(11) NOT NULL,
  `UserAccountTypeId` int(11) DEFAULT NULL,
  `UserId` int(11) DEFAULT NULL,
  `File` varchar(600) DEFAULT NULL,
  `CreateDate` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `coaches`
--

CREATE TABLE `coaches` (
  `Id` int(11) NOT NULL,
  `Name` varchar(255) DEFAULT NULL,
  `IdNumber` int(11) DEFAULT NULL,
  `QRcode` varchar(255) DEFAULT NULL,
  `Phone` varchar(255) DEFAULT NULL,
  `Email` varchar(255) DEFAULT NULL,
  `CityName` varchar(255) DEFAULT NULL,
  `Location` varchar(255) DEFAULT NULL,
  `TeamId` int(11) DEFAULT NULL,
  `CoachType` varchar(255) DEFAULT NULL,
  `Rating` double DEFAULT 0,
  `DeviceType` varchar(255) DEFAULT NULL,
  `TokenId` varchar(500) DEFAULT NULL,
  `Lang` varchar(100) DEFAULT NULL,
  `LastActive` datetime DEFAULT NULL,
  `E-PaymentInfoId` int(11) DEFAULT NULL,
  `CreateDate` datetime DEFAULT NULL,
  `Status` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `commentators`
--

CREATE TABLE `commentators` (
  `Id` int(11) NOT NULL,
  `Name` varchar(255) DEFAULT NULL,
  `IdNumber` int(11) DEFAULT NULL,
  `QRcode` varchar(255) DEFAULT NULL,
  `Phone` varchar(255) DEFAULT NULL,
  `Email` varchar(255) DEFAULT NULL,
  `CityName` varchar(255) DEFAULT NULL,
  `Location` varchar(255) DEFAULT NULL,
  `Rating` double DEFAULT 0,
  `DeviceType` varchar(255) DEFAULT NULL,
  `TokenId` varchar(500) DEFAULT NULL,
  `Lang` varchar(100) DEFAULT NULL,
  `LastActive` datetime DEFAULT NULL,
  `E-PaymentInfoId` int(11) DEFAULT NULL,
  `CreateDate` datetime DEFAULT NULL,
  `Status` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `e-paymentinfo`
--

CREATE TABLE `e-paymentinfo` (
  `Id` int(11) NOT NULL,
  `UserId` int(11) DEFAULT NULL,
  `UserAccountTypeId` int(11) DEFAULT NULL,
  `BankName` varchar(255) DEFAULT NULL,
  `BankAccount` varchar(255) DEFAULT NULL,
  `BeneficiaryName` varchar(255) DEFAULT NULL,
  `BankIdBIC` varchar(255) DEFAULT NULL,
  `TransferOption` varchar(255) DEFAULT NULL,
  `CreateDate` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `exercises`
--

CREATE TABLE `exercises` (
  `Id` int(11) NOT NULL,
  `Topic` varchar(255) DEFAULT NULL,
  `ExerciseType` varchar(255) DEFAULT NULL,
  `Details` varchar(455) DEFAULT NULL,
  `QRcode` varchar(255) DEFAULT NULL,
  `ExerciseDate` datetime DEFAULT NULL,
  `CityName` varchar(255) DEFAULT NULL,
  `Location` varchar(255) DEFAULT NULL,
  `StadiumId` int(11) DEFAULT NULL,
  `Fee` double DEFAULT 0,
  `CreatById` int(11) DEFAULT NULL,
  `UserAccountTypeId` int(11) DEFAULT NULL,
  `CreateDate` datetime DEFAULT NULL,
  `Status` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `footballerpositions`
--

CREATE TABLE `footballerpositions` (
  `Id` int(11) NOT NULL,
  `PlayerId` int(11) DEFAULT NULL,
  `Position` varchar(255) DEFAULT NULL,
  `CreateDate` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `leagues`
--

CREATE TABLE `leagues` (
  `Id` int(11) NOT NULL,
  `Topic` varchar(255) DEFAULT NULL,
  `Details` varbinary(455) DEFAULT NULL,
  `QRcode` varchar(255) DEFAULT NULL,
  `StadiumId` int(11) DEFAULT NULL,
  `CityName` varchar(255) DEFAULT NULL,
  `Location` varchar(255) DEFAULT NULL,
  `Fee` double DEFAULT 0,
  `CreateDate` datetime DEFAULT NULL,
  `Status` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `leagueteams`
--

CREATE TABLE `leagueteams` (
  `Id` int(11) NOT NULL,
  `LeagueId` int(11) DEFAULT NULL,
  `FirstTeamId` int(11) DEFAULT NULL,
  `SecondTeamId` int(11) DEFAULT NULL,
  `MatchDate` datetime DEFAULT NULL,
  `StadiumId` int(11) DEFAULT NULL,
  `CreateDate` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `matches`
--

CREATE TABLE `matches` (
  `Id` int(11) NOT NULL,
  `LeagueTeamId` int(11) DEFAULT NULL,
  `Name` varchar(255) DEFAULT NULL,
  `Details` varchar(455) DEFAULT NULL,
  `MatchTypeId` int(11) DEFAULT NULL,
  `QRcode` varchar(255) DEFAULT NULL,
  `StadiumId` int(11) DEFAULT NULL,
  `FirstTeamId` int(11) DEFAULT NULL,
  `CommentatorId` int(11) DEFAULT NULL,
  `CityName` varchar(255) DEFAULT NULL,
  `Location` varchar(255) DEFAULT NULL,
  `MatchDate` datetime DEFAULT NULL,
  `CreateDate` datetime DEFAULT NULL,
  `Status` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `matchstype`
--

CREATE TABLE `matchstype` (
  `Id` int(11) NOT NULL,
  `NameAr` varchar(255) DEFAULT NULL,
  `NameEn` varchar(255) DEFAULT NULL,
  `Status` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ordersitems`
--

CREATE TABLE `ordersitems` (
  `Id` int(11) NOT NULL,
  `OrderId` int(11) DEFAULT NULL,
  `ProductId` int(11) DEFAULT NULL,
  `Quntity` int(11) DEFAULT NULL,
  `Price` double DEFAULT NULL,
  `Tax` double DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `organizers`
--

CREATE TABLE `organizers` (
  `Id` int(11) NOT NULL,
  `IdNumber` varchar(255) DEFAULT NULL,
  `Name` varchar(255) DEFAULT NULL,
  `Phone` varchar(255) DEFAULT NULL,
  `Email` varchar(255) DEFAULT NULL,
  `CityName` varchar(255) DEFAULT NULL,
  `Location` varchar(255) DEFAULT NULL,
  `OrganizeCategory` varchar(255) DEFAULT NULL,
  `Rating` double DEFAULT 0,
  `DeviceType` varchar(255) DEFAULT NULL,
  `TokenId` varchar(500) DEFAULT NULL,
  `Lang` varchar(100) DEFAULT NULL,
  `LastActive` datetime DEFAULT NULL,
  `E-PaymentInfoId` int(11) DEFAULT NULL,
  `CreateDate` datetime DEFAULT NULL,
  `Status` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `organizers`
--

INSERT INTO `organizers` (`Id`, `IdNumber`, `Name`, `Phone`, `Email`, `CityName`, `Location`, `OrganizeCategory`, `Rating`, `DeviceType`, `TokenId`, `Lang`, `LastActive`, `E-PaymentInfoId`, `CreateDate`, `Status`) VALUES
(1, '1451111', 'طارق', '052222222', 't@t.com', NULL, NULL, 'منظم بوابة', 0, NULL, NULL, NULL, '2022-06-06 20:38:23', NULL, '2022-06-06 20:38:23', 0),
(2, '1111', 'جمال', '054784554', 'j@j.com', NULL, NULL, 'كور', 0, NULL, NULL, NULL, '2022-06-07 18:33:06', NULL, '2022-06-07 18:33:06', 1);

-- --------------------------------------------------------

--
-- Table structure for table `organizerslist`
--

CREATE TABLE `organizerslist` (
  `Id` int(11) NOT NULL,
  `MatchId` int(11) DEFAULT NULL,
  `organizerId` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `paymentmethod`
--

CREATE TABLE `paymentmethod` (
  `Id` int(11) NOT NULL,
  `NameAr` varchar(255) DEFAULT NULL,
  `NameEn` varchar(255) DEFAULT NULL,
  `Status` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `Id` int(11) NOT NULL,
  `Amount` double DEFAULT NULL,
  `PaymentMethodId` int(11) DEFAULT NULL,
  `PaymentStatus` int(11) DEFAULT 0,
  `CreateDate` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `photographers`
--

CREATE TABLE `photographers` (
  `Id` int(11) NOT NULL,
  `Name` varchar(255) DEFAULT NULL,
  `IdNumber` int(11) DEFAULT NULL,
  `QRcode` varchar(255) DEFAULT NULL,
  `Phone` varchar(255) DEFAULT NULL,
  `Email` varchar(255) DEFAULT NULL,
  `CityName` varchar(255) DEFAULT NULL,
  `Location` varchar(255) DEFAULT NULL,
  `Rating` double DEFAULT 0,
  `DeviceType` varchar(255) DEFAULT NULL,
  `TokenId` varchar(500) DEFAULT NULL,
  `Lang` varchar(100) DEFAULT NULL,
  `LastActive` datetime DEFAULT NULL,
  `E-PaymentInfoId` int(11) DEFAULT NULL,
  `CreateDate` datetime DEFAULT NULL,
  `Status` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `physiotherapyclinics`
--

CREATE TABLE `physiotherapyclinics` (
  `Id` int(11) NOT NULL,
  `IdNumber` varchar(255) DEFAULT NULL,
  `Name` varchar(255) DEFAULT NULL,
  `Phone` varchar(255) DEFAULT NULL,
  `Email` varchar(255) DEFAULT NULL,
  `CR` varchar(255) DEFAULT NULL,
  `QRcode` varchar(255) DEFAULT NULL,
  `CityName` varchar(255) DEFAULT NULL,
  `Location` varchar(255) DEFAULT NULL,
  `Rating` varchar(255) DEFAULT NULL,
  `DeviceType` varchar(255) DEFAULT NULL,
  `TokenId` varchar(500) DEFAULT NULL,
  `Lang` varchar(100) DEFAULT NULL,
  `LastActive` datetime DEFAULT NULL,
  `E-PaymentInfoId` int(11) DEFAULT NULL,
  `CreateDate` datetime DEFAULT NULL,
  `Status` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `players`
--

CREATE TABLE `players` (
  `Id` int(11) NOT NULL,
  `Name` varchar(255) DEFAULT NULL,
  `IdNumber` int(11) DEFAULT NULL,
  `QRcode` varchar(255) DEFAULT NULL,
  `Phone` varchar(255) DEFAULT NULL,
  `Email` varchar(255) DEFAULT NULL,
  `CityName` varchar(255) DEFAULT NULL,
  `Location` varchar(255) DEFAULT NULL,
  `TeamId` int(11) DEFAULT NULL,
  `PositionId` int(11) DEFAULT NULL,
  `Rating` double DEFAULT 0,
  `DeviceType` varchar(255) DEFAULT NULL,
  `TokenId` varchar(500) DEFAULT NULL,
  `Lang` varchar(100) DEFAULT NULL,
  `LastActive` datetime DEFAULT NULL,
  `CreateDate` datetime DEFAULT NULL,
  `Status` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `playertransfers`
--

CREATE TABLE `playertransfers` (
  `Id` int(11) NOT NULL,
  `PlayerId` int(11) DEFAULT NULL,
  `FromTeamId` int(11) DEFAULT NULL,
  `ToTeamId` int(11) DEFAULT NULL,
  `Amount` double DEFAULT NULL,
  `IsApproved` int(11) DEFAULT 0,
  `ApprovedBy` int(11) DEFAULT 0,
  `ApprovedDate` datetime DEFAULT NULL,
  `PaymentId` int(11) DEFAULT 0,
  `CreateDate` datetime DEFAULT NULL,
  `Status` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `referees`
--

CREATE TABLE `referees` (
  `Id` int(11) NOT NULL,
  `Name` varchar(255) DEFAULT NULL,
  `IdNumber` int(11) DEFAULT NULL,
  `QRcode` varchar(255) DEFAULT NULL,
  `Phone` varchar(255) DEFAULT NULL,
  `Email` varchar(255) DEFAULT NULL,
  `CityName` varchar(255) DEFAULT NULL,
  `Location` varchar(255) DEFAULT NULL,
  `Rating` double DEFAULT 0,
  `RefereeType` varchar(255) DEFAULT NULL,
  `DeviceType` varchar(255) DEFAULT NULL,
  `TokenId` varchar(500) DEFAULT NULL,
  `Lang` varchar(100) DEFAULT NULL,
  `LastActive` datetime DEFAULT NULL,
  `E-PaymentInfoId` int(11) DEFAULT NULL,
  `CreateDate` datetime DEFAULT NULL,
  `Status` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `refereeslist`
--

CREATE TABLE `refereeslist` (
  `Id` int(11) NOT NULL,
  `MatchId` int(11) DEFAULT NULL,
  `RefereeId` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `scoutsofclubs`
--

CREATE TABLE `scoutsofclubs` (
  `Id` int(11) NOT NULL,
  `Name` varchar(255) DEFAULT NULL,
  `IdNumber` int(11) DEFAULT NULL,
  `Phone` varchar(255) DEFAULT NULL,
  `Email` varchar(255) DEFAULT NULL,
  `CityName` varchar(255) DEFAULT NULL,
  `Location` varchar(255) DEFAULT NULL,
  `TeamId` int(11) DEFAULT NULL,
  `CreateDate` datetime DEFAULT NULL,
  `Status` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `sponsors`
--

CREATE TABLE `sponsors` (
  `Id` int(11) NOT NULL,
  `CR` varchar(255) DEFAULT NULL,
  `Name` varchar(255) DEFAULT NULL,
  `Phone` varchar(255) DEFAULT NULL,
  `Email` varchar(255) DEFAULT NULL,
  `QRcode` varchar(255) DEFAULT NULL,
  `Status` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `stadiums`
--

CREATE TABLE `stadiums` (
  `Id` int(11) NOT NULL,
  `Logo` varchar(255) DEFAULT NULL,
  `Name` varchar(255) DEFAULT NULL,
  `CR` varchar(255) DEFAULT NULL,
  `QRcode` varchar(255) DEFAULT NULL,
  `Phone` varchar(255) DEFAULT NULL,
  `Email` varchar(255) DEFAULT NULL,
  `CityName` varchar(255) DEFAULT NULL,
  `Location` varchar(255) DEFAULT NULL,
  `Details` varchar(455) DEFAULT NULL,
  `Fee` double DEFAULT 0,
  `Rating` double DEFAULT 0,
  `E-PaymentInfoId` int(11) DEFAULT NULL,
  `CreateDate` datetime DEFAULT NULL,
  `Status` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `stadiumsbooking`
--

CREATE TABLE `stadiumsbooking` (
  `Id` int(11) NOT NULL,
  `StadiumId` int(11) DEFAULT NULL,
  `MatchDate` datetime DEFAULT NULL,
  `FirstTeamId` int(11) DEFAULT NULL,
  `SecondTeamId` int(11) DEFAULT NULL,
  `PaymentId` int(11) DEFAULT 0,
  `CreateDate` datetime DEFAULT NULL,
  `Status` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `storecategories`
--

CREATE TABLE `storecategories` (
  `Id` int(11) NOT NULL,
  `NameAr` varchar(255) DEFAULT NULL,
  `NameEn` varchar(255) DEFAULT NULL,
  `Image` varchar(255) DEFAULT NULL,
  `CreateDate` datetime DEFAULT NULL,
  `Status` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `stores`
--

CREATE TABLE `stores` (
  `Id` int(11) NOT NULL,
  `StoreCategoryId` int(11) DEFAULT NULL,
  `Name` varchar(255) DEFAULT NULL,
  `Phone` varchar(255) DEFAULT NULL,
  `Email` varchar(200) NOT NULL,
  `QRCode` varchar(255) DEFAULT NULL,
  `OwnerName` varchar(255) DEFAULT NULL,
  `OwnerPhone` varchar(255) DEFAULT NULL,
  `CityName` varchar(255) DEFAULT NULL,
  `Location` varchar(255) DEFAULT NULL,
  `E-PaymentInfoId` int(11) DEFAULT NULL,
  `CreateDate` datetime DEFAULT NULL,
  `Status` int(11) DEFAULT 1,
  `IsTax` int(11) DEFAULT 0,
  `TaxNumber` varchar(255) DEFAULT NULL,
  `PaymentTypes` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `storesorders`
--

CREATE TABLE `storesorders` (
  `Id` int(11) NOT NULL,
  `StoreId` int(11) DEFAULT NULL,
  `UserAccountTypeId` int(11) DEFAULT NULL,
  `UserId` int(11) DEFAULT NULL,
  `Location` varchar(255) DEFAULT NULL,
  `PaymentMethodId` int(11) DEFAULT NULL,
  `PaymentStatus` varchar(255) DEFAULT NULL,
  `PaymentId` int(11) DEFAULT NULL,
  `DeliveryCost` double DEFAULT 0,
  `TransactionId` varchar(255) DEFAULT NULL,
  `IsDelete` int(11) DEFAULT 0,
  `CreateDate` datetime DEFAULT NULL,
  `Status` varchar(255) DEFAULT 'New'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `storesproducts`
--

CREATE TABLE `storesproducts` (
  `Id` int(11) NOT NULL,
  `StoreId` int(11) DEFAULT NULL,
  `NameAr` varchar(255) DEFAULT NULL,
  `NameEn` varchar(255) DEFAULT NULL,
  `Image` varchar(455) DEFAULT NULL,
  `Details` varchar(455) DEFAULT NULL,
  `Price` double DEFAULT NULL,
  `CreateDate` datetime DEFAULT NULL,
  `Status` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `storesproductscategories`
--

CREATE TABLE `storesproductscategories` (
  `Id` int(11) NOT NULL,
  `StoreId` int(11) DEFAULT NULL,
  `NameAr` varchar(255) DEFAULT NULL,
  `NameEn` varchar(255) DEFAULT NULL,
  `Image` varchar(255) DEFAULT NULL,
  `CreateDate` datetime DEFAULT NULL,
  `Status` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `supervisors`
--

CREATE TABLE `supervisors` (
  `Id` int(11) NOT NULL,
  `Name` varchar(255) DEFAULT NULL,
  `IdNumber` varchar(255) DEFAULT NULL,
  `Phone` varchar(255) DEFAULT NULL,
  `Email` varchar(255) DEFAULT NULL,
  `CityName` varchar(255) DEFAULT NULL,
  `Location` varchar(255) DEFAULT NULL,
  `AreaRange` double NOT NULL DEFAULT 0,
  `Rating` double DEFAULT 0,
  `DeviceType` varchar(255) DEFAULT NULL,
  `TokenId` varchar(500) DEFAULT NULL,
  `Lang` varchar(100) DEFAULT NULL,
  `LastActive` datetime DEFAULT NULL,
  `CreateDate` datetime DEFAULT NULL,
  `Status` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `supervisors`
--

INSERT INTO `supervisors` (`Id`, `Name`, `IdNumber`, `Phone`, `Email`, `CityName`, `Location`, `AreaRange`, `Rating`, `DeviceType`, `TokenId`, `Lang`, `LastActive`, `CreateDate`, `Status`) VALUES
(1, 'محمد', '10111411', '0566491219', 'm@m.com', 'Alula', '26.558470175593108, 37.96554573651946', 0, 0, NULL, NULL, NULL, '2022-06-04 17:47:54', '2022-06-04 17:47:54', 0),
(2, 'نايف', '14555', '05478544555', 'n@n.com', 'Alula', NULL, 0, 0, NULL, NULL, NULL, '2022-06-06 20:29:52', '2022-06-06 20:29:52', 1);

-- --------------------------------------------------------

--
-- Table structure for table `teams`
--

CREATE TABLE `teams` (
  `Id` int(11) NOT NULL,
  `Logo` varchar(255) DEFAULT NULL,
  `QRcode` varchar(255) DEFAULT NULL,
  `CR` varchar(255) DEFAULT NULL,
  `Name` varchar(255) DEFAULT NULL,
  `CaptainId` int(11) DEFAULT NULL,
  `TeamLeaderId` int(11) DEFAULT NULL,
  `CityName` varchar(255) DEFAULT NULL,
  `Location` varchar(255) DEFAULT NULL,
  `ChatUrl` varchar(255) DEFAULT NULL,
  `E-PaymentInfoId` int(11) DEFAULT NULL,
  `CreateDate` datetime DEFAULT NULL,
  `Status` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `useraccountstype`
--

CREATE TABLE `useraccountstype` (
  `Id` int(11) NOT NULL,
  `Name` varchar(255) DEFAULT NULL,
  `CreateDate` datetime DEFAULT NULL,
  `Status` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `useraccountstype`
--

INSERT INTO `useraccountstype` (`Id`, `Name`, `CreateDate`, `Status`) VALUES
(1, 'مشرف', '2022-06-06 22:04:13', 1),
(2, 'منظم', '2022-06-06 22:04:13', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `advertisingagencies`
--
ALTER TABLE `advertisingagencies`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `attachments`
--
ALTER TABLE `attachments`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `coaches`
--
ALTER TABLE `coaches`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `commentators`
--
ALTER TABLE `commentators`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `e-paymentinfo`
--
ALTER TABLE `e-paymentinfo`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `exercises`
--
ALTER TABLE `exercises`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `footballerpositions`
--
ALTER TABLE `footballerpositions`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `leagues`
--
ALTER TABLE `leagues`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `leagueteams`
--
ALTER TABLE `leagueteams`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `matches`
--
ALTER TABLE `matches`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `matchstype`
--
ALTER TABLE `matchstype`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `ordersitems`
--
ALTER TABLE `ordersitems`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `organizers`
--
ALTER TABLE `organizers`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `organizerslist`
--
ALTER TABLE `organizerslist`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `paymentmethod`
--
ALTER TABLE `paymentmethod`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `photographers`
--
ALTER TABLE `photographers`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `physiotherapyclinics`
--
ALTER TABLE `physiotherapyclinics`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `players`
--
ALTER TABLE `players`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `playertransfers`
--
ALTER TABLE `playertransfers`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `referees`
--
ALTER TABLE `referees`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `refereeslist`
--
ALTER TABLE `refereeslist`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `scoutsofclubs`
--
ALTER TABLE `scoutsofclubs`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `sponsors`
--
ALTER TABLE `sponsors`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `stadiums`
--
ALTER TABLE `stadiums`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `stadiumsbooking`
--
ALTER TABLE `stadiumsbooking`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `storecategories`
--
ALTER TABLE `storecategories`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `stores`
--
ALTER TABLE `stores`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `storesorders`
--
ALTER TABLE `storesorders`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `storesproducts`
--
ALTER TABLE `storesproducts`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `storesproductscategories`
--
ALTER TABLE `storesproductscategories`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `supervisors`
--
ALTER TABLE `supervisors`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `teams`
--
ALTER TABLE `teams`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `useraccountstype`
--
ALTER TABLE `useraccountstype`
  ADD PRIMARY KEY (`Id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `advertisingagencies`
--
ALTER TABLE `advertisingagencies`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `attachments`
--
ALTER TABLE `attachments`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `coaches`
--
ALTER TABLE `coaches`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `commentators`
--
ALTER TABLE `commentators`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `e-paymentinfo`
--
ALTER TABLE `e-paymentinfo`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `exercises`
--
ALTER TABLE `exercises`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `footballerpositions`
--
ALTER TABLE `footballerpositions`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `leagues`
--
ALTER TABLE `leagues`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `leagueteams`
--
ALTER TABLE `leagueteams`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `matches`
--
ALTER TABLE `matches`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `matchstype`
--
ALTER TABLE `matchstype`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ordersitems`
--
ALTER TABLE `ordersitems`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `organizers`
--
ALTER TABLE `organizers`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `organizerslist`
--
ALTER TABLE `organizerslist`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `paymentmethod`
--
ALTER TABLE `paymentmethod`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `photographers`
--
ALTER TABLE `photographers`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `physiotherapyclinics`
--
ALTER TABLE `physiotherapyclinics`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `players`
--
ALTER TABLE `players`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `playertransfers`
--
ALTER TABLE `playertransfers`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `referees`
--
ALTER TABLE `referees`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `refereeslist`
--
ALTER TABLE `refereeslist`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `scoutsofclubs`
--
ALTER TABLE `scoutsofclubs`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sponsors`
--
ALTER TABLE `sponsors`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `stadiums`
--
ALTER TABLE `stadiums`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `stadiumsbooking`
--
ALTER TABLE `stadiumsbooking`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `storecategories`
--
ALTER TABLE `storecategories`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `stores`
--
ALTER TABLE `stores`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `storesorders`
--
ALTER TABLE `storesorders`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `storesproducts`
--
ALTER TABLE `storesproducts`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `storesproductscategories`
--
ALTER TABLE `storesproductscategories`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `supervisors`
--
ALTER TABLE `supervisors`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `teams`
--
ALTER TABLE `teams`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `useraccountstype`
--
ALTER TABLE `useraccountstype`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
