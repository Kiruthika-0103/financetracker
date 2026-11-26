OUR FINANCE TRACKER - தொடங்குவதற்கான வழிகாட்டி
================================================

இந்த project ஒரு ஆரம்ப MVP. இரண்டு login accounts ஒரே shared finance data-ஐப் பார்க்கும்.
PHP + MySQL தேவை. Delivery apps-லிருந்து தானாக data எடுக்காது; earnings-ஐ நீங்களே பதிவு செய்ய வேண்டும்.

LOCAL-ஆக இயக்குவது (XAMPP)
--------------------------
1. XAMPP install செய்து Apache, MySQL இரண்டையும் Start செய்யவும்.
2. இந்த folder-ஐ C:\xampp\htdocs\personal_finance_tracker என்ற இடத்தில் வைக்கவும்.
3. Browser-ல் http://localhost/phpmyadmin திறக்கவும்.
4. config.example.php-ஐ copy செய்து config.php என்று பெயர் மாற்றவும்.
5. config.php-ல் DB_NAME=personal_finance, DB_USER=root, DB_PASS='' என்று உள்ளதா பார்க்கவும்.
6. http://localhost/personal_finance_tracker/setup.php திறக்கவும்.
7. இருவருக்கும் பெயர், username, 8+ character password கொடுத்து Setup செய்யவும்.
8. Setup முடிந்ததும் setup.php-ஐ delete செய்யவும்.
9. http://localhost/personal_finance_tracker/login.php திறந்து login செய்யவும்.

ONLINE HOSTING
--------------
1. PHP 8.1+ மற்றும் MySQL/MariaDB உள்ள hosting தேர்வு செய்யவும்.
2. Hosting control panel-ல் database + database user உருவாக்கி, password set செய்யவும்.
3. config.example.php-ஐ config.php என மாற்றி hosting database details உள்ளிடவும்.
4. Project files-ஐ hosting-ன் public_html அல்லது domain document root-க்கு upload செய்யவும்.
5. HTTPS/SSL enable செய்யவும்.
6. https://your-domain/setup.php திறந்து இரு user accounts உருவாக்கவும்.
7. Setup முடிந்தவுடன் setup.php-ஐ உடனே delete செய்யவும்.
8. login.php மூலம் login செய்து பயன்படுத்தவும்.

முக்கிய பாதுகாப்பு குறிப்புகள்
----------------------------
- config.php-ஐ யாரிடமும் பகிர வேண்டாம்.
- வலுவான, வேறுபட்ட passwords பயன்படுத்தவும்.
- Hosting-ல் HTTPS கட்டாயம் enable செய்யவும்.
- Database backup-ஐ வாரம் ஒருமுறை download செய்து பாதுகாப்பாக வைக்கவும்.
- Public/shared computer-ல் login செய்தால் logout செய்யவும்.
- Free hosting-ல் data retention, backup, uptime உறுதி இருக்காது. முக்கியமான வாழ்நாள் பதிவுகளுக்கு தனியாக backup அவசியம்.

இப்போதைய அம்சங்கள்
-----------------
- இருவருக்கும் தனித்தனி login; shared entries
- Manage Apps: புதிய App சேர்க்க, பெயர் மாற்ற, Hide/Activate, Delete
- App-wise income பதிவு
- Expense மற்றும் category பதிவு
- Savings பதிவு
- Rides / orders எண்ணிக்கை
- மாதாந்திர summary மற்றும் recent entries
- Car savings goal (setup நேரத்தில் target அமைக்கலாம்)

அடுத்த கட்டத்தில் சேர்க்கக்கூடியவை
---------------------------------
- Entry edit/delete
- Date filter மற்றும் daily/weekly/yearly charts
- CSV/Excel export
- Expense split மற்றும் தனிப்பட்ட/கூட்டு செலவு
- Automatic database backup
- Password reset
