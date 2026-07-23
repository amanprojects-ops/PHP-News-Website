CREATE TABLE `category` (
  `category_id` int(11) NOT NULL,
  `category_name` varchar(100) DEFAULT NULL,
  `categoryTitle` varchar(225) DEFAULT NULL,
  `categoryStatus` varchar(10) NOT NULL DEFAULT 'W',
  `author` int(11) DEFAULT NULL,
  `categoryDate` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`category_id`, `category_name`, `categoryTitle`, `categoryStatus`, `author`, `categoryDate`) VALUES
(1, 'RESULTS', 'All Types Exam Result, Exam Result Marksheet Download Linked, All Types Results Exam Notification', 'Y', 2, '2023-06-14 14:36:23'),
(2, 'Latest Update', 'Latest Jobs & Vacancy Update, Job Application Form, Job Application Notification and News posts', 'Y', 1, '2023-06-14 17:20:21'),
(3, 'ADMIT CARDS', 'Students Admit Card Or Hall Tickets Or Admit Card Notification.', 'Y', 5, '2023-06-14 17:24:52'),
(4, 'Admission Form', 'Admission Form Update', 'Y', 5, '2023-06-14 17:26:43'),
(5, 'Latest News', 'Latest News', 'Y', 2, '2023-10-26 11:44:53'),
(6, 'NEWS ARTICLES', 'Food News, Latest News, Viral News, Current News,etc', 'Y', 6, '2023-11-03 17:37:54');

-- --------------------------------------------------------

--
-- Table structure for table `contact_us`
--

CREATE TABLE `contact_us` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(30) NOT NULL,
  `email` varchar(50) NOT NULL,
  `subject` varchar(100) NOT NULL,
  `message` text NOT NULL,
  `created_at` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `post`
--

CREATE TABLE `post` (
  `post_id` int(11) NOT NULL,
  `title` varchar(1500) DEFAULT NULL,
  `post_slug` varchar(255) DEFAULT NULL,
  `sort_details` varchar(250) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `post_img` varchar(225) DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `author` int(11) DEFAULT NULL,
  `postStatus` varchar(12) NOT NULL DEFAULT 'W',
  `post_date` varchar(50) DEFAULT NULL,
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_description` text DEFAULT NULL,
  `meta_keywords` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `websitename` varchar(60) DEFAULT NULL,
  `favicon` varchar(255) DEFAULT NULL,
  `logo` varchar(50) DEFAULT NULL,
  `footerdesc` varchar(255) DEFAULT NULL,
  `keywords` varchar(1000) DEFAULT NULL,
  `watterMark` varchar(191) DEFAULT NULL,
  `workEmail` varchar(191) DEFAULT NULL,
  `websiteTitle` varchar(225) DEFAULT NULL,
  `websiteUrl` varchar(999) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`websitename`, `favicon`, `logo`, `footerdesc`, `keywords`, `watterMark`, `workEmail`, `websiteTitle`, `websiteUrl`) VALUES
('ConnectBihar', '1682677942-slogo.png', 'logo.png', 'ConnectBihar.in', 'Sarkari Result, Connect Bihar: ConnectBihar.in provides latest Sarkari Result Jobs, Online Form, Sarkari Naukri Result in Sarkari Result 2023 various sectors such as Railway, Bank, SSC, Navy, Police, UPPSC, UPSSSC, UPTET, UP Scholarship ,\r\nConnect Bihar.in बिहार की सभी भर्ती की जानकारी देता है | Bihar Job Portal, Vacancy, Bihar Job Alert, Bihar Govt Job, Bihar Career Portal,\r\nBihar Govt Jobs Notifications 2023 apply at connectbihar.in Bihar Government Jobs here for all qualifications like 10th, 12th, technical and Any Degree etc. and latest Job updates are listed here', '1682677972-watter_mark.png', 'websiteowner@connectbihar.in', NULL, 'https://connectbihar.in');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `user_id` int(10) UNSIGNED NOT NULL,
  `username` varchar(30) DEFAULT NULL,
  `first_name` varchar(30) NOT NULL,
  `last_name` varchar(30) NOT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `email` varchar(225) DEFAULT NULL,
  `password` varchar(40) DEFAULT NULL,
  `role` int(11) NOT NULL DEFAULT 0,
  `userStatus` varchar(10) NOT NULL DEFAULT 'W',
  `rDate` timestamp NOT NULL DEFAULT current_timestamp(),
  `taken` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`user_id`, `username`, `first_name`, `last_name`, `phone`, `email`, `password`, `role`, `userStatus`, `rDate`, `taken`) VALUES
(1, 'websiteowner', 'Website', 'Owner', '8544002323', 'websiteOwner@connectbihar.in', '351f2356ba2521a79a6a923edd071878', 1, 'Y', '2023-04-28 10:13:34', 0),
(2, 'Technicalbaba', 'Technical', 'Aman', '9473366936', 'aman@connectbihar.in', '21232f297a57a5a743894a0e4a801fc3', 1, 'Y', '2023-04-28 10:45:06', 1),
(3, 'Lucky', 'Lucky', 'Mafiya', '7320863727', 'luckymafiya@gmail.com', '81dc9bdb52d04dc20036dbd8313ed055', 3, 'N', '2023-04-28 10:48:15', 2),
(5, 'mr.mayavi', 'Mr.', 'MaYavi', '8271193337', 'Mr.mayavi@gmail.com', '81dc9bdb52d04dc20036dbd8313ed055', 3, 'Y', '2023-06-14 14:39:39', 2),
(6, 'govind', 'Govind', 'Kumar', '8507777790', 'govid@connectbihar.in', '2654f4a1c04731cd0b9a50382d5031cd', 2, 'Y', '2023-10-25 14:03:48', 2),
(8, 'saurabhraj', 'Saurabh', 'Raj', '9241593288', 'saurabhsinghaa95@gmail.com', '81dc9bdb52d04dc20036dbd8313ed055', 2, 'Y', '2023-12-10 04:48:27', 2),
(9, 'kingboyroshan', 'King Boy', 'Roshan', '9142492516', 'roshankumarjha8@gmail.com', '81dc9bdb52d04dc20036dbd8313ed055', 3, 'Y', '2024-02-08 10:10:15', 2);

-- --------------------------------------------------------

--
-- Table structure for table `visitors`
--

CREATE TABLE `visitors` (
  `id` int(255) NOT NULL,
  `ip` longtext NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;


--
-- Indexes for table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `contact_us`
--
ALTER TABLE `contact_us`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `post`
--
ALTER TABLE `post`
  ADD PRIMARY KEY (`post_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `visitors`
--
ALTER TABLE `visitors`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `category`
--
ALTER TABLE `category`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `contact_us`
--
ALTER TABLE `contact_us`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `post`
--
ALTER TABLE `post`
  MODIFY `post_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `visitors`
--
ALTER TABLE `visitors`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=214048;
COMMIT;

