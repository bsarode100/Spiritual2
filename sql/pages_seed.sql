-- =====================================================================
-- pages_seed.sql
--
-- Re-seeds the four protected policy pages (privacy, terms,
-- refund-policy, cookie-policy) with comprehensive, original copy
-- tuned for an Indian spiritual matrimony platform.
--
-- Idempotent: safe to run on a fresh DB or an existing one. Uses
-- INSERT ... ON DUPLICATE KEY UPDATE on the `slug` unique key.
-- Admins can still freely edit the body from /admin/pages — re-running
-- this migration will overwrite their changes, so run it deliberately.
--
-- Reference statutes assumed:
--   - Information Technology Act, 2000 (and Intermediary Guidelines 2021)
--   - Digital Personal Data Protection Act, 2023 (DPDP Act)
--   - Consumer Protection Act, 2019 (and E-Commerce Rules 2020)
--   - Reserve Bank of India guidelines for online payments
--
-- The legal entity name and address are pulled from the public-facing
-- settings (site_name, contact_email, contact_address, contact_phone).
-- The admin can refine those at /admin/settings without touching this file.
-- =====================================================================

-- ---------- 1. PRIVACY POLICY ----------
INSERT INTO `pages` (`slug`, `title`, `body`, `published`) VALUES
('privacy', 'Privacy Policy',
'<p class=''lead''>Your privacy is sacred to us. This Privacy Policy describes how we collect, use, share and safeguard the personal information you share with us when you create a profile, browse other members, exchange interests, send messages, or pay for a membership on our spiritual matrimony platform.</p>
<p><em>Last reviewed on 1 January 2026. This policy is published in accordance with Rule 3(1) of the Information Technology (Intermediary Guidelines and Digital Media Ethics Code) Rules, 2021 and Section 5 of the Digital Personal Data Protection Act, 2023.</em></p>

<h3>1. Who We Are</h3>
<p>References to "we", "us", "our" or "the platform" mean the operator of this website as identified in the footer. We are an online matrimonial introduction service for sincere spiritual seekers — sadhakas, devotees, yogis and dharma-rooted families looking for a like-minded life-partner. We are an intermediary under Section 2(1)(w) of the Information Technology Act, 2000 and a Data Fiduciary under the DPDP Act, 2023.</p>

<h3>2. Information You Provide Directly</h3>
<p>When you register and use the platform, you knowingly share the following categories of personal data:</p>
<ul>
  <li><strong>Account identifiers</strong> — full name, email address, mobile number and password.</li>
  <li><strong>Matrimonial profile</strong> — date of birth, gender, marital status, height, mother tongue, religion, community, caste, gotra, manglik status, family type and family status.</li>
  <li><strong>Location</strong> — country, state and city of residence.</li>
  <li><strong>Vocation</strong> — education, profession, annual income range.</li>
  <li><strong>Spiritual identity</strong> — guru lineage, ishta-devata, daily sadhana, favourite scripture, fasting practice, pilgrimages completed, mantra or initiation, diet (vegetarian, sattvic, vegan or other) and spiritual path.</li>
  <li><strong>Free-form text</strong> — your "about me" introduction and your partner preferences.</li>
  <li><strong>Media</strong> — photographs you upload to your profile or use as your primary picture.</li>
  <li><strong>Horoscope details</strong> — when you choose to add them.</li>
  <li><strong>Communication</strong> — interests sent or received, shortlists and any messages exchanged through our in-built messenger.</li>
  <li><strong>Payment metadata</strong> — when you buy a membership, our payment gateway shares with us the transaction reference, package, amount, currency and status. We never see or store your full card number, CVV, UPI PIN or net-banking password.</li>
</ul>

<h3>3. Information Collected Automatically</h3>
<p>When you visit any page, our servers automatically record technical data needed to keep the service safe and working:</p>
<ul>
  <li>Internet Protocol (IP) address and approximate location derived from it.</li>
  <li>Device type, operating system, browser version and screen size.</li>
  <li>Pages requested, the order in which you viewed them and the time spent on each.</li>
  <li>Referring website (if any) and search terms that brought you to us.</li>
  <li>Session cookies and small first-party identifiers used to keep you signed in. Read our separate <a href=''/cookies''>Cookie Policy</a> for the full list.</li>
</ul>

<h3>4. Why We Use Your Information</h3>
<p>We process your personal data only for clearly defined matrimonial purposes:</p>
<ul>
  <li><strong>Profile creation and matching</strong> — to display your profile to compatible members of the opposite gender who match your spiritual path, age, location and lifestyle filters.</li>
  <li><strong>Connection workflow</strong> — to handle interests, shortlists, accept/decline actions and the private messenger between members who mutually accept.</li>
  <li><strong>Subscriptions and billing</strong> — to process membership payments, confirm activation, generate receipts and renew or expire plans.</li>
  <li><strong>Service notifications</strong> — to send transactional emails or SMS about interests received, accepted matches, password resets and important account events.</li>
  <li><strong>Trust and safety</strong> — to detect duplicate accounts, impersonation, fake profiles, harassment, automated scraping and other abuse.</li>
  <li><strong>Customer support</strong> — to answer your queries when you contact us via the <a href=''/contact''>Contact</a> page.</li>
  <li><strong>Legal compliance</strong> — to retain transaction and identity records to the extent required by Indian tax, anti-money-laundering and intermediary law.</li>
</ul>

<h3>5. Lawful Basis</h3>
<p>Under the DPDP Act, 2023 we process your personal data on the basis of your <em>specific, informed and free consent</em> at the time of registration, and on the basis of <em>legitimate uses</em> such as the performance of the matrimonial service you signed up for, compliance with a legal obligation, and prevention or detection of fraud or abuse. You may withdraw consent at any time by writing to us — see Section 11 below — although we may need to retain limited records to comply with law.</p>

<h3>6. What Other Members See</h3>
<p>The whole point of a matrimonial platform is that other genuine seekers can find you. By default, the following fields from your profile are visible to other signed-in members of the opposite gender:</p>
<ul>
  <li>Your first name and the year (not the full date) of your birth.</li>
  <li>City, state and country.</li>
  <li>Religion, community, mother tongue and spiritual path.</li>
  <li>Education, profession and a generalised income range.</li>
  <li>Your "about me" introduction and partner preferences.</li>
  <li>Your photographs, unless you have marked them private.</li>
</ul>
<p>The following are <strong>never</strong> shown to any other member or to any third party:</p>
<ul>
  <li>Your email address, mobile number, exact street address and password.</li>
  <li>Your payment details, subscription history and any internal admin notes about your account.</li>
</ul>
<p>Communication between members is routed entirely through our in-app messenger. Members exchange direct contact details only after they themselves decide to.</p>

<h3>7. Sharing With Third Parties</h3>
<p>We do not sell, rent, trade or barter your personal data. We share it only in the narrow situations below:</p>
<ul>
  <li><strong>Payment gateway</strong> — when you purchase a membership, the gateway (currently Razorpay or any successor) receives the data needed to process that single transaction.</li>
  <li><strong>Cloud infrastructure</strong> — our hosting, email and SMS providers process data on our behalf under written confidentiality and data-protection terms.</li>
  <li><strong>Law enforcement and courts</strong> — only when we receive a valid, written and legally enforceable request under Indian law, and only to the extent demanded.</li>
  <li><strong>Successor in interest</strong> — if the platform is ever transferred to another responsible entity, your data will be transferred with equivalent protection, and you will be notified before any material change in purpose.</li>
</ul>

<h3>8. Cookies and Similar Technologies</h3>
<p>We use a small number of strictly first-party cookies and local storage entries — chiefly the PHP session cookie used to keep you signed in, a CSRF token used to protect form submissions, and a remember-filter cookie used to preserve your last search. We do not use third-party advertising cookies. The full list and shelf-life of each cookie is published on our <a href=''/cookies''>Cookie Policy</a> page.</p>

<h3>9. Data Retention</h3>
<p>We retain your account, profile, photos and message history for as long as your account is active. When you delete your account, we delete or irreversibly anonymise your personal data within <strong>thirty (30) days</strong>, except for the minimum we are required to retain by law (typically payment receipts and tax records, which we keep for the statutory period). Backups containing your data are overwritten in the normal course within sixty (60) days.</p>

<h3>10. Security</h3>
<p>We take the following technical and organisational measures to safeguard your data:</p>
<ul>
  <li>TLS / HTTPS encryption for every page and API request.</li>
  <li>Passwords stored only as bcrypt hashes — they are never recoverable in plain text, not even by our administrators.</li>
  <li>Role-based access — only authorised administrators can see member records, and every administrative action is logged.</li>
  <li>CSRF tokens, parameterised SQL and content security policies to defend against common web attacks.</li>
  <li>Periodic backups stored in a separate access zone.</li>
</ul>
<p>No system on the internet is perfectly secure, however. If we ever become aware of a breach that materially affects you, we will notify you and the Data Protection Board of India in accordance with Section 8(6) of the DPDP Act, 2023.</p>

<h3>11. Your Rights</h3>
<p>Under Indian data-protection law you have the right to:</p>
<ul>
  <li><strong>Access</strong> — view all the personal data we hold about you, available any time from your profile dashboard.</li>
  <li><strong>Correct</strong> — edit any field on your profile yourself, at no charge.</li>
  <li><strong>Erase</strong> — request deletion of your account through the <a href=''/contact''>Contact</a> page; deletion proceeds within thirty days.</li>
  <li><strong>Restrict</strong> — temporarily pause or hide your profile by switching it to "private" in account settings.</li>
  <li><strong>Withdraw consent</strong> — to non-essential communications without losing access to your membership.</li>
  <li><strong>Nominate</strong> — appoint a nominee (a family member or trusted friend) to act on your behalf under the DPDP Act in the event of your death or incapacity.</li>
  <li><strong>Grievance</strong> — escalate any complaint that we have not resolved to your satisfaction.</li>
</ul>

<h3>12. Children</h3>
<p>The platform is meant strictly for adults of legal marriageable age. We do not knowingly collect personal data from anyone under the age of 18. If you believe a minor has created an account, please write to us immediately and we will remove the profile.</p>

<h3>13. Changes to This Policy</h3>
<p>We may update this Privacy Policy from time to time to reflect changes in law, our practices, or the features we offer. We will publish the revised policy on this page with a new "Last reviewed" date and, for any material change, we will also notify you by email or by a banner inside your dashboard at least <strong>fifteen (15) days</strong> before the change takes effect.</p>

<h3>14. Contact &amp; Grievance Redressal</h3>
<p>For any question, request or complaint about this Privacy Policy or your personal data, please write to us through the <a href=''/contact''>Contact</a> page or send an email to the address listed in the website footer. We aim to acknowledge every grievance within <strong>twenty-four (24) hours</strong> of receipt and to resolve it within <strong>fifteen (15) days</strong>, in line with Rule 3(2) of the Intermediary Guidelines, 2021. The contact details and name of the Grievance Officer are published on our <a href=''/contact''>Contact</a> page.</p>
<p>If you are not satisfied with our response, you may also approach the Data Protection Board of India once it is operational, under Section 27 of the DPDP Act, 2023.</p>',
1)
ON DUPLICATE KEY UPDATE
  `title` = VALUES(`title`),
  `body`  = VALUES(`body`),
  `published` = 1;


-- ---------- 2. TERMS OF SERVICE ----------
INSERT INTO `pages` (`slug`, `title`, `body`, `published`) VALUES
('terms', 'Terms of Service',
'<p class=''lead''>Welcome to our spiritual matrimony platform. These Terms of Service form a binding contract between you and us. By creating an account, browsing other profiles, sending an interest, exchanging messages or buying a membership you confirm that you have read, understood and accepted these terms in full.</p>
<p><em>Last reviewed on 1 January 2026. Published electronically under Section 10A of the Information Technology Act, 2000.</em></p>

<h3>1. Definitions</h3>
<ul>
  <li>"Platform", "Service", "we", "us" or "our" — the website and any mobile app operated by us under the name shown in the footer.</li>
  <li>"Member" or "you" — any natural person who has registered an account with us.</li>
  <li>"Profile" — the matrimonial information you publish, including photographs, partner preferences and spiritual details.</li>
  <li>"Content" — any text, image, photo, message or other material posted, transmitted or stored on the Platform by you.</li>
</ul>

<h3>2. Eligibility</h3>
<p>To register and use the Service you must:</p>
<ul>
  <li>be at least <strong>18 years</strong> old if female and <strong>21 years</strong> old if male — the minimum legal marriageable ages prescribed under the Prohibition of Child Marriage Act, 2006;</li>
  <li>be legally competent to enter a marriage under the personal law applicable to you;</li>
  <li>be looking for a life-partner for marriage, and not for any casual, commercial, fraudulent or non-matrimonial purpose;</li>
  <li>not have previously had an account terminated by us for violation of these terms.</li>
</ul>
<p>By registering you represent and warrant that all of the above is true. Creating a profile on behalf of another adult is permitted only with that adult''s express written authorisation and a clear declaration of the relationship (for example, a parent registering on behalf of a son or daughter).</p>

<h3>3. Honest Self-Representation</h3>
<p>The whole spirit of a matrimony platform rests on truthfulness. You agree that:</p>
<ul>
  <li>All information you submit — name, age, marital status, religion, community, profession, income, spiritual practice, photos — is current, accurate, and yours.</li>
  <li>You will not upload photographs of any other person, edited or AI-generated images that mislead about your appearance, or pictures from your past that no longer represent you.</li>
  <li>You will not conceal a previous marriage, an existing relationship, a serious medical condition, or any other material fact a reasonable prospective partner would want to know.</li>
  <li>You will keep your profile updated when material facts change (city, marital status, profession and so on).</li>
</ul>
<p>Wilful misrepresentation is a ground for immediate account termination and, in serious cases, may attract action under Sections 415–420 of the Bharatiya Nyaya Sanhita (cheating) or the corresponding provisions of the Information Technology Act, 2000.</p>

<h3>4. Acceptable Use</h3>
<p>You agree to use the Platform with the same respect you would bring to a temple, a satsang or any sacred space:</p>
<ul>
  <li>Do not post or transmit content that is obscene, sexually explicit, defamatory, hateful, casteist, threatening or unlawful.</li>
  <li>Do not solicit money, gifts, investments, jobs, donations or business of any kind from other members.</li>
  <li>Do not send interests or messages in bulk through any automated means; do not scrape, copy, mirror or republish any profile data.</li>
  <li>Do not impersonate any other person, deity, guru, organisation or government authority.</li>
  <li>Do not contact a member who has declined your interest or asked to be left alone; repeated unwanted contact is harassment.</li>
  <li>Do not bypass our messenger to push members on to other channels until they themselves choose to share contact details.</li>
  <li>Do not attempt to interfere with, probe, reverse-engineer or exhaust the Service in any way.</li>
</ul>
<p>Where any provision of these terms overlaps with Rule 3(1)(b) of the Intermediary Guidelines, 2021 — for instance the prohibitions on harmful, paedophilic, hateful, or unlawful content — the statutory provision applies in full force.</p>

<h3>5. Account Security</h3>
<p>You are responsible for keeping your password confidential and for every activity that occurs under your account. Notify us at once through the <a href=''/contact''>Contact</a> page if you suspect any unauthorised access. We will never call or email you asking for your password, OTP or payment PIN — anyone who does is impersonating us, and you should report them to us immediately.</p>

<h3>6. Memberships, Pricing and Payments</h3>
<p>Profile creation, basic browse and limited messaging are free. Premium features — extended messaging, contact reveal, photo unlock, profile boost and others — require a paid plan listed on the <a href=''/packages''>Packages</a> page. All amounts are in Indian Rupees (INR) unless otherwise indicated and are inclusive of applicable Goods and Services Tax (GST) where notified.</p>
<p>Payments can be made online through our integrated gateway (Razorpay) by UPI, debit/credit card or net banking, or directly to the bank/UPI account listed on the <a href=''/payment-details''>Payment Details</a> page. A plan becomes active only after we receive verified confirmation of payment. Memberships are personal to you and may not be transferred, gifted or resold.</p>

<h3>7. Refunds and Cancellation</h3>
<p>The full refund and cancellation rules are published on the separate <a href=''/refund-policy''>Refund &amp; Cancellation Policy</a> page, which forms an integral part of these Terms. In short: once a paid plan is activated, fees are generally non-refundable; however, we honour clear, time-bound exceptions for double payments, technical failures and certain dissatisfaction windows — please read the policy in full before paying.</p>

<h3>8. Intellectual Property</h3>
<p>The Platform itself — its design, code, logo, written text, illustrations and curated editorial content (such as blog posts and happy stories) — is owned by us or licensed to us, and is protected under the Copyright Act, 1957 and the Trade Marks Act, 1999. You may not copy, modify, distribute or create derivative works from it without our prior written permission.</p>
<p>You retain ownership of the content you submit (photos, "about me", spiritual details). By submitting it, you grant us a worldwide, royalty-free, non-exclusive licence to host, display, reformat, and excerpt it strictly for the purpose of operating the Service. Subject to your privacy settings, we may also use a non-identifying excerpt of your story (without your photograph or full name) for happy-stories editorial content — but only after you give explicit written consent at that time.</p>

<h3>9. Member-to-Member Interactions</h3>
<p>We act only as an introduction platform. We do not background-check every member, we do not solemnise marriages and we are not a marriage bureau in the traditional sense. Decisions to share contact information, meet in person or proceed to engagement and marriage are entirely your own. Please apply common-sense matrimonial diligence: verify identity through family channels, meet in safe places, involve your parents or guardians, and never share OTPs, banking details or large sums of money on the basis of a profile alone.</p>

<h3>10. Disclaimers</h3>
<p>The Service is provided on an "as-is" and "as-available" basis. To the maximum extent permitted by law:</p>
<ul>
  <li>We make no warranty that the Service will be uninterrupted, error-free or perfectly secure.</li>
  <li>We make no warranty that any introduction will lead to engagement, marriage or any specific outcome.</li>
  <li>We make no warranty about the accuracy of any profile other than our own ongoing best efforts to detect and remove fakes.</li>
</ul>

<h3>11. Limitation of Liability</h3>
<p>To the maximum extent permitted by law, our total aggregate liability arising out of or in connection with these Terms or your use of the Service shall not exceed the total amount of membership fees you have paid us in the twelve (12) months immediately preceding the event giving rise to the claim. We shall not be liable for any indirect, incidental, special, punitive or consequential loss, including loss of relationship, reputation, savings or anticipated happiness.</p>

<h3>12. Suspension &amp; Termination</h3>
<p>We may, in our reasonable discretion and with or without prior notice, suspend or terminate your account if:</p>
<ul>
  <li>you breach any provision of these Terms or our community guidelines;</li>
  <li>we receive credible complaints from other members about your conduct;</li>
  <li>we detect indicators of fraud, fake identity or automated abuse;</li>
  <li>we are required to do so by a court, regulator or law-enforcement authority.</li>
</ul>
<p>You may close your account at any time from the <a href=''/contact''>Contact</a> page. Fees paid for an active period are generally non-refundable on termination — see the <a href=''/refund-policy''>Refund &amp; Cancellation Policy</a>.</p>

<h3>13. Indemnity</h3>
<p>You agree to indemnify and hold us, our employees and our service providers harmless from any claim, demand, loss or expense (including reasonable legal fees) arising out of your breach of these Terms, your content, or your interaction with any other member.</p>

<h3>14. Governing Law and Jurisdiction</h3>
<p>These Terms are governed by the laws of the Republic of India. Any dispute, controversy or claim arising out of or relating to these Terms shall be resolved by amicable discussion first; failing which, it shall be submitted to the exclusive jurisdiction of the competent civil courts at the location of our registered office, as published on the <a href=''/contact''>Contact</a> page.</p>

<h3>15. Changes to These Terms</h3>
<p>We may amend these Terms from time to time. The revised Terms will be posted on this page with a new "Last reviewed" date. For material changes, we will additionally notify you by email or by an in-dashboard banner at least <strong>fifteen (15) days</strong> before the change takes effect. Continued use of the Service after the effective date constitutes acceptance.</p>

<h3>16. Grievance Officer</h3>
<p>In accordance with Rule 3(2) of the Intermediary Guidelines, 2021, the name and contact details of our Grievance Officer are published on the <a href=''/contact''>Contact</a> page. The Grievance Officer will acknowledge complaints within twenty-four (24) hours and resolve them within fifteen (15) days from the date of receipt.</p>

<h3>17. Entire Agreement</h3>
<p>These Terms, together with the <a href=''/privacy''>Privacy Policy</a>, the <a href=''/refund-policy''>Refund &amp; Cancellation Policy</a>, the <a href=''/cookies''>Cookie Policy</a> and any plan-specific terms stated on the <a href=''/packages''>Packages</a> page at the time you purchase, constitute the entire agreement between you and us with respect to the Service.</p>',
1)
ON DUPLICATE KEY UPDATE
  `title` = VALUES(`title`),
  `body`  = VALUES(`body`),
  `published` = 1;


-- ---------- 3. REFUND & CANCELLATION POLICY ----------
INSERT INTO `pages` (`slug`, `title`, `body`, `published`) VALUES
('refund-policy', 'Refund & Cancellation Policy',
'<p class=''lead''>Marriage is a serious decision, and so is the trust you place in us when you pay for a membership. This Refund &amp; Cancellation Policy explains exactly when you are entitled to a refund, how to request one, and how soon you can expect the money to return to you.</p>
<p><em>Last reviewed on 1 January 2026. This policy supplements our <a href=''/terms''>Terms of Service</a> and is published in line with the Consumer Protection (E-Commerce) Rules, 2020 and the Reserve Bank of India guidelines applicable to our payment gateway.</em></p>

<h3>1. Free Membership</h3>
<p>Account creation, profile setup, browsing and limited messaging are free. Nothing is charged at registration. You may use the free tier for as long as you like and close your account at any time without any payment, paperwork or notice period.</p>

<h3>2. When a Paid Membership Becomes Final</h3>
<p>A paid membership is treated as <em>activated</em> the moment our system records a successful payment from the gateway (Razorpay) and unlocks any one of the included premium benefits — for example, the moment a contact is revealed, a private photograph is unlocked, an interest is sent under the paid quota, or an extended chat is opened. From that moment, the membership is in use and the fees become non-refundable, subject only to the specific exceptions in Section 3 below.</p>

<h3>3. Eligible Refund Scenarios</h3>
<p>You are entitled to a refund — full or partial as applicable — in the following well-defined situations:</p>
<ul>
  <li><strong>Duplicate or accidental payment.</strong> If you were charged twice for the same plan, or paid against the wrong package, the excess amount will be refunded in full. Write to us within seven (7) days with the transaction reference.</li>
  <li><strong>Payment debited, membership not activated.</strong> If your bank or UPI confirms the debit but the platform did not activate the plan within twenty-four (24) hours, we will either activate the plan manually or refund the full amount, at your option.</li>
  <li><strong>Technical failure on our side.</strong> If a verified bug on our platform prevented you from using any of the core paid features for a continuous period of more than five (5) working days, we will offer either a free extension equal to the lost period, or a pro-rata refund for that period.</li>
  <li><strong>Account terminated by us without a violation by you.</strong> If we suspend or terminate your paid membership for an internal business reason that is not attributable to a breach of our <a href=''/terms''>Terms of Service</a> by you, we will refund the unused balance of your plan on a pro-rata basis.</li>
  <li><strong>Statutory rights.</strong> Where any non-waivable consumer-protection law applicable to you requires a refund that is broader than the above, that statutory right applies in full.</li>
</ul>

<h3>4. Situations Where Refunds Are Not Available</h3>
<p>To keep the platform sustainable and fair to all members, refunds will <strong>not</strong> be processed in any of the following circumstances:</p>
<ul>
  <li>Change of mind, change of personal situation, or finding a partner through any other source after you have paid.</li>
  <li>Your account being suspended or terminated for a breach of our <a href=''/terms''>Terms of Service</a> — including fake profiles, misrepresentation, harassment of other members, soliciting money or running any non-matrimonial purpose.</li>
  <li>Dissatisfaction with the number of matches received. We make introductions based on your filters and the live pool of members; we do not promise a specific number of responses, interests accepted or contact reveals.</li>
  <li>Failure to use the membership during the validity period. Memberships expire at the end of the duration listed on the <a href=''/packages''>Packages</a> page even if unused.</li>
  <li>Profile rejection by other members; matrimonial outcomes are subjective and outside our control.</li>
  <li>Add-on services explicitly marked as non-refundable on the package page at the time of purchase (for example, one-time profile-boost credits already consumed).</li>
</ul>

<h3>5. How to Request a Refund</h3>
<p>Send us a written request through the <a href=''/contact''>Contact</a> page, or email the support address listed in the website footer. Include:</p>
<ul>
  <li>The email address registered on your account;</li>
  <li>The package name and the date of payment;</li>
  <li>The Razorpay payment ID, bank reference number or UPI transaction ID;</li>
  <li>A clear explanation of the eligible refund scenario from Section 3 that you believe applies to you;</li>
  <li>Any supporting screenshots that help us investigate quickly.</li>
</ul>
<p>We will acknowledge every refund request within <strong>two (2) working days</strong>.</p>

<h3>6. Timeline for Refund Processing</h3>
<p>Once your refund is approved:</p>
<ul>
  <li><strong>Razorpay refunds</strong> are typically initiated within five (5) working days of approval and credited by your bank within an additional five to seven working days, depending on your card issuer or UPI app.</li>
  <li><strong>UPI / direct bank transfer refunds</strong> are sent back to the same account from which payment was received, within seven (7) working days of approval.</li>
</ul>
<p>The refund will always be processed to the original payment source. If that account is closed, we will require fresh KYC details before issuing an alternate transfer.</p>

<h3>7. Cancellation of a Paid Plan</h3>
<p>You may cancel a paid plan at any time by writing to us through the <a href=''/contact''>Contact</a> page or by deleting your account from account settings. Cancellation immediately removes the auto-renew flag (where one exists) and stops further charges, but it does <strong>not</strong> by itself trigger a refund of fees already paid for the current period, unless one of the eligible scenarios in Section 3 applies.</p>

<h3>8. Chargebacks and Disputes</h3>
<p>If you initiate a chargeback through your bank without first writing to us, we reserve the right to suspend your account while the dispute is being investigated. We strongly recommend that you contact us first — most issues are resolved within a day or two and do not need to escalate to a chargeback.</p>

<h3>9. Changes to This Policy</h3>
<p>We may update this Refund &amp; Cancellation Policy from time to time. The revised policy will be posted on this page with a new "Last reviewed" date. Refund requests are always evaluated under the policy in force at the time the original payment was made.</p>

<h3>10. Contact</h3>
<p>For any refund query, please write to us through the <a href=''/contact''>Contact</a> page. We aim to settle every fair refund quickly and quietly — we would much rather you remember us as honourable than score a small accounting victory.</p>',
1)
ON DUPLICATE KEY UPDATE
  `title` = VALUES(`title`),
  `body`  = VALUES(`body`),
  `published` = 1;


-- ---------- 4. COOKIE POLICY ----------
INSERT INTO `pages` (`slug`, `title`, `body`, `published`) VALUES
('cookie-policy', 'Cookie Policy',
'<p class=''lead''>This Cookie Policy explains what cookies are, the small handful that we set on your device, and the choices you have about them. It supplements — and should be read together with — our <a href=''/privacy''>Privacy Policy</a> and <a href=''/terms''>Terms of Service</a>.</p>
<p><em>Last reviewed on 1 January 2026.</em></p>

<h3>1. What Is a Cookie?</h3>
<p>A "cookie" is a small text file that a website asks your browser to store on your device. Each time you return to the same website, your browser sends the cookie back, allowing the site to recognise you, remember your preferences and keep you signed in. Modern browsers also support related technologies — local storage, session storage and the IndexedDB — which behave in a similar way. We use the word "cookie" loosely in this policy to cover all of these.</p>

<h3>2. Our Philosophy on Cookies</h3>
<p>We treat cookies the same way a careful host treats a guest''s shoes at the door: as little intrusion as possible, with full disclosure. We use only <strong>first-party</strong> cookies — set by our own domain — and we set them only for purposes that are strictly necessary to operate the service or that you have explicitly asked for. We do <strong>not</strong> use third-party advertising trackers, cross-site profiling cookies, or any cookie that follows you around the wider internet.</p>

<h3>3. The Cookies We Set</h3>
<p>The complete list of cookies and similar identifiers we currently use is given below. Names and durations may change with software updates — we will keep this list current.</p>
<table style=''width:100%; border-collapse:collapse; margin: 1rem 0;''>
  <thead>
    <tr style=''background: var(--c-cream-2, #f6efe2);''>
      <th style=''text-align:left; padding:.6rem; border:1px solid #e6dcc6;''>Name</th>
      <th style=''text-align:left; padding:.6rem; border:1px solid #e6dcc6;''>Purpose</th>
      <th style=''text-align:left; padding:.6rem; border:1px solid #e6dcc6;''>Category</th>
      <th style=''text-align:left; padding:.6rem; border:1px solid #e6dcc6;''>Lifetime</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td style=''padding:.6rem; border:1px solid #e6dcc6;''><code>PHPSESSID</code></td>
      <td style=''padding:.6rem; border:1px solid #e6dcc6;''>Keeps you signed in for the duration of your visit and links you to your shopping/checkout session.</td>
      <td style=''padding:.6rem; border:1px solid #e6dcc6;''>Strictly necessary</td>
      <td style=''padding:.6rem; border:1px solid #e6dcc6;''>Session (cleared when you close the browser)</td>
    </tr>
    <tr>
      <td style=''padding:.6rem; border:1px solid #e6dcc6;''><code>csrf_token</code></td>
      <td style=''padding:.6rem; border:1px solid #e6dcc6;''>Protects every form submission against cross-site request forgery attacks.</td>
      <td style=''padding:.6rem; border:1px solid #e6dcc6;''>Strictly necessary</td>
      <td style=''padding:.6rem; border:1px solid #e6dcc6;''>Session</td>
    </tr>
    <tr>
      <td style=''padding:.6rem; border:1px solid #e6dcc6;''><code>remember_filters</code></td>
      <td style=''padding:.6rem; border:1px solid #e6dcc6;''>Optional — remembers your last browse filters (city, age range, spiritual path) so you do not have to set them each time.</td>
      <td style=''padding:.6rem; border:1px solid #e6dcc6;''>Functional / Preference</td>
      <td style=''padding:.6rem; border:1px solid #e6dcc6;''>30 days</td>
    </tr>
    <tr>
      <td style=''padding:.6rem; border:1px solid #e6dcc6;''><code>cookie_consent</code></td>
      <td style=''padding:.6rem; border:1px solid #e6dcc6;''>Records that you have seen and acknowledged this cookie notice, so we do not show it again on every page.</td>
      <td style=''padding:.6rem; border:1px solid #e6dcc6;''>Strictly necessary</td>
      <td style=''padding:.6rem; border:1px solid #e6dcc6;''>12 months</td>
    </tr>
    <tr>
      <td style=''padding:.6rem; border:1px solid #e6dcc6;''>Razorpay checkout</td>
      <td style=''padding:.6rem; border:1px solid #e6dcc6;''>When you choose to pay online, Razorpay sets its own cookies inside its secure iframe to process the payment. These are governed by Razorpay''s own privacy policy.</td>
      <td style=''padding:.6rem; border:1px solid #e6dcc6;''>Third-party (payment only)</td>
      <td style=''padding:.6rem; border:1px solid #e6dcc6;''>As per Razorpay</td>
    </tr>
  </tbody>
</table>

<h3>4. Cookies We Deliberately Do Not Use</h3>
<ul>
  <li>Google Analytics, Facebook Pixel, TikTok Pixel or any other behavioural-advertising tracker.</li>
  <li>Cross-site identifiers used for re-targeting.</li>
  <li>Fingerprinting scripts that try to identify your device without a cookie at all.</li>
</ul>

<h3>5. Your Choices</h3>
<p>You can manage cookies at any time through your browser settings. The "Help" or "Settings" menu of every major browser (Chrome, Safari, Firefox, Edge, Brave) lets you:</p>
<ul>
  <li>See and delete the cookies already stored;</li>
  <li>Block all cookies from a specific site;</li>
  <li>Be prompted before any cookie is set.</li>
</ul>
<p>Please note: if you block our strictly necessary cookies, parts of the platform — login, messaging, checkout — will simply stop working. The Service is not designed to function without them.</p>

<h3>6. Changes to This Policy</h3>
<p>If we add, remove or materially change the way any cookie works, we will update this page and the table in Section 3, and we will re-issue the consent notice the next time you visit. Continued use of the platform indicates your acceptance of the updated policy.</p>

<h3>7. Contact</h3>
<p>If you have any question about how we use cookies, please write to us through the <a href=''/contact''>Contact</a> page.</p>',
1)
ON DUPLICATE KEY UPDATE
  `title` = VALUES(`title`),
  `body`  = VALUES(`body`),
  `published` = 1;


-- =====================================================================
-- End of pages_seed.sql
-- =====================================================================
