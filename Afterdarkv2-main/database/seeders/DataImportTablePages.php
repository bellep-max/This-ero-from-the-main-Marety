<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Support\Facades\DB;

class DataImportTablePages extends DataImportTable
{
    protected array $toUpdate = [ 2 ];

    protected function runMore() {
        $this->command->info('Updating pages...');

        $pages = Page::whereIn('id', $this->toUpdate)->get();

        foreach ($pages as $page) {
            $data = $this->getUpdate($page->id);

            $this->command->info('Updating page #' . $page->id . ' "' . $page->title  .'" columns: ' . implode(', ', array_keys($data)) . '.');

            // This throws an exception about created_at suddenly being a string
            //$page->fill($data);
            //$page->update($data);

            // Done manually
            $data['updated_at'] = date('Y-m-d H:i:s');

            $query = 'UPDATE pages SET ';
            $queryCols = [];
            $queryData = [];

            foreach ($data as $k => $v) {
                $queryCols[] = $k . ' = ?';
                $queryData[] = $v;
            }

            $query .= implode(', ', $queryCols);

            $query .= ' WHERE id = ?';
            $queryData[] = $page->id;

            DB::update($query, $queryData);
        }
    }

    protected function getUpdate(int $id) {

        $data = [];

        if ($id == 2) {

$data['content'] = <<<EOT
<h1>TERMS OF SERVICE​</h1>

<h2>After Dark Audio</h2>

<h3>1. INTRODUCTION AND BINDING AGREEMENT</h3>

<p>These Terms of Service ("Terms" or "Agreement") govern your use of the After Dark Audio platform (the "Platform"), operated by Entek Solutions, LLC, a Utah Limited Liability Company, doing business as After Dark Audio ("we," "us," "our," or "Company").</p>

<p>BY ACCESSING, USING, OR REGISTERING WITH THE PLATFORM, YOU ACKNOWLEDGE THAT YOU HAVE READ, UNDERSTOOD, AND AGREE TO BE BOUND BY THESE TERMS OF SERVICE, OUR PRIVACY POLICY, AND ALL INCORPORATED POLICIES AND GUIDELINES. If you do not agree to these Terms, you may not access or use the Platform.</p>

<p>These Terms constitute the entire agreement between you and After Dark Audio regarding your use of the Platform and supersede all prior negotiations, representations, and agreements, whether written or oral, between the parties.</p>

<p>We reserve the right to modify these Terms at any time. Material changes will be communicated to you via email or prominent notice on the Platform. Your continued use of the Platform following notification of changes constitutes your acceptance of the modified Terms. If you do not accept the changes, your sole remedy is to discontinue use of the Platform.</p>

<hr />

<h3>2. DEFINITIONS</h3>

<p>For purposes of these Terms:</p>

<ul>
<li>​"Account" means a user account created on the Platform.</li>
<li>"Content" means any material uploaded to the Platform, including but not limited to audio files, descriptions, metadata, images, text, and any other materials.</li>
<li>"Creator" means a User who uploads and monetizes Content on the Platform.</li>
<li>"Creator Earnings" means the fees earned by a Creator from Subscriber payments, after deducting our fees, payment processing fees, and applicable taxes.</li>
<li>"Creator Interaction" means any transaction between a Subscriber and a Creator, including monthly subscriptions, tiered subscriptions, or any other paid access to Content.</li>
<li>​"Subscriber" means a User who pays for access to a Creator's exclusive Content.</li>
<li>"Subscription" means a recurring monthly payment for access to a Creator's exclusive Content.</li>
<li>"Subscription Fee" means the monthly price set by a Creator for their exclusive Content.</li>
<li>"User" means any person who accesses or uses the Platform, whether as a Subscriber, Creator, or both.</li>
<li>"We," "Us," "Our," or "Company" means Entek Solutions, LLC, doing business as After Dark Audio.</li>
</ul>

<hr />

<h3>3. ELIGIBILITY AND REGISTRATION</h3>

<h4>3.1 Age Requirements</h4>

<p>To use the Platform, you must:</p>
<ul>
<li>Be at least 18 years of age (or the age of majority in your jurisdiction, whichever is greater);</li>
<li>Not be prohibited by law or court order from using the Platform;</li>
<li>Not have been previously suspended or terminated from the Platform;</li>
<li>Have the legal capacity to be bound by these Terms.</li>
</ul>

<h4>3.2 Age and Identity Verification</h4>

<p>For all Creators: You must provide valid government-issued identification and proof of age before your account is activated. Verification documents may include a passport, driver's license, or national ID card. This information is required to comply with payment processor requirements and applicable laws governing adult content platforms.</p>

<p>For Subscribers: Age verification is required before accessing any Content. We employ age verification technologies and may request additional documentation to confirm your eligibility to access adult content.</p>

<p>Retention of Verification Information: After age and identity verification is completed, personally identifiable information from verification documents will not be retained beyond what is legally required. Your verification status will be recorded, but sensitive document details will be securely deleted in accordance with our Privacy Policy.</p>

<hr />

<h4>3.3 Registration Process</h4>

<p>To register an Account:</p>

<ol>
<li>Provide accurate, complete, and current information as requested;</li>
<li>Maintain and update your information to ensure accuracy;</li>
<li>Create a secure password that meets our security requirements;</li>
<li>For Creators: Provide valid banking information for payouts and complete tax documentation as required by law;</li>
<li>Agree to these Terms and all incorporated policies.</li>
</ol>

<p>You are responsible for all activities conducted through your Account. You agree to maintain the confidentiality of your password and not to share access to your Account with any third party. If you suspect unauthorized access to your Account, you must notify us immediately.</p>

<p>We may reject any account application for any reason, including but not limited to:</p>

<ul>
<li>​Violation of these Terms;</li>
<li>Suspicious or fraudulent activity;</li>
<li>Age verification failure;</li>
<li>Failure to comply with legal requirements;</li>
<li>Prior violations of Platform policies.</li>
</ul>

<hr />

<h3>4. ACCEPTABLE CONTENT AND PROHIBITED CONTENT</h3>

<h4>4.1 Acceptable Content Categories</h4>
<p>After Dark Audio permits the following types of Content:</p>

<ul>
<li>ASMR (Autonomous Sensory Meridian Response) content;</li>
<li>Guided meditations and relaxation recordings;</li>
<li>Original fictional stories (including romantic and sexual audio content);</li>
<li>Educational audio content;</li>
<li>Romantic and sexual audio content between consenting adults.</li>
</ul>

<p>All Content must comply with applicable laws in all jurisdictions where it is accessed.</p>

<h4>4.2 Prohibited Content</h4>

<p>The following content is strictly prohibited and will result in immediate content removal and potential account termination:</p>
<p>Illegal Content:</p>

<ul>
<li>​Any content that violates applicable laws in North America, the European Union, or any jurisdiction in which the Content is accessed;</li>
<li>Child exploitation, child sexual abuse material (CSAM), or any sexualized depictions of minors in any form;</li>
<li>Content involving non-consensual acts or depicting rape, sexual assault, or coercion;</li>
<li>Content depicting illegal drug manufacturing, distribution, or use;</li>
<li>Content facilitating illegal gambling, fraud, or money laundering;</li>
<li>Content related to human trafficking or slavery.</li>
</ul>

<p>Harmful and Violent Content:</p>

<ul>
<li>Extreme violence, gore, graphic depictions of injury, or self-harm;</li>
<li>Content depicting or promoting violence against any person or group;</li>
<li>Content glorifying or promoting self-harm, suicide, or dangerous activities;</li>
<li>Snuff content or depictions of death;</li>
<li>Bestiality or zoophilia content.</li>
</ul>

<p>Non-Consensual and Exploitative Content:</p>

<ul>
<li>Non-consensual intimate imagery (revenge porn);</li>
<li>Deepfakes or manipulated audio/video of real individuals without consent;</li>
<li>Content depicting abuse, exploitation, or coercion;</li>
<li>Extortion, blackmail, or threatening content;</li>
<li>Doxxing or sharing of private personal information intended to harm;</li>
<li>Harassment, bullying, or targeting of individuals or groups.</li>
</ul>

<p>Hate Speech and Discrimination:</p>

<ul>
<li>Content promoting hatred based on race, ethnicity, national origin, religion, gender identity, sexual orientation, disability, or other protected characteristics;</li>
<li>Slurs, dehumanizing language, or incitement to violence against any group;</li>
<li>Conspiracy theories promoting violence or discrimination.</li>
</ul>

<p>Intellectual Property Violations:</p>

<ul>
<li>Unauthorized use of copyrighted material, trademarks, or third-party intellectual property;</li>
<li>​Content you do not own or have rights to distribute;</li>
<li>Stolen or pirated content from other creators or platforms.</li>
</ul>

<p>Incest Content:</p>

<ul>
<li>​Any content depicting or describing sexual activity between family members, including step-relations and in-laws, regardless of whether such conduct is legal in any jurisdiction;</li>
<li>Content that simulates or depicts familial relationships in a sexual context.</li>
</ul>

<p>Other Prohibited Content:</p>

<ul>
<li>Impersonation of another person or entity;</li>
<li>False or misleading content designed to deceive;</li>
<li>Spam, advertising, or promotional content unrelated to your Creator profile;</li>
<li>Malware, viruses, or code intended to harm the Platform or Users;</li>
<li>Threats, intimidation, or harassment.</li>
</ul>

<h4>4.3 Content Responsibility and Warranties</h4>

<p>You are solely and legally responsible for all Content you upload. If you are a Creator and another individual assists with account operation, you remain fully responsible for all Content and compliance with these Terms.</p>

<p>You warrant that:</p>

<ul>
<li>All Content complies with these Terms and applicable laws;</li>
<li>You own all intellectual property rights in your Content, or hold all necessary licenses and permissions;</li>
<li>If your Content includes or uses third-party material (music, sound effects, voice actors, etc.), you have obtained all required rights, licenses, written consents, and releases;</li>
<li>Your Content does not infringe any third-party intellectual property, privacy, publicity, or other rights;</li>
<li>All individuals appearing in or contributing to your Content have provided informed, documented consent;</li>
<li>For fictional content involving characters: any resemblance to real individuals is coincidental, and the content does not depict real, named individuals without consent;</li>
<li>You will comply with all applicable laws in all jurisdictions where your Content is accessed.</li>
</ul>

<p>You agree to indemnify and hold harmless After Dark Audio from any claims, damages, losses, or expenses (including attorney's fees) arising from your breach of these warranties.</p>

<h4>4.4 Age and Consent Requirements for Content</h4>

<p>All Creators must ensure:</p>

<ol>
<li>Creator Age: You are at least 18 years old and have provided valid age verification;</li>
<li>Content Subject Age: Any person depicted, referenced, or whose voice is used in your Content must be at least 18 years of age;</li>
<li>Consent: All individuals who appear in or contribute to your Content (including through voice, story, or reference) must have provided explicit, informed, documented written consent before Content is uploaded;</li>
<li>Fictional Content: If your Content is entirely fictional with fictional characters, you may proceed without individual consent, provided the content does not reference, depict, or target real, named individuals.</li>
</ol>

<hr />

<h3>5. SUBSCRIPTIONS AND BILLING</h3>

<h4>5.1 Subscription Model</h4>

<p>After Dark Audio operates on a monthly subscription model. Creators set their Subscription Fees, and Subscribers pay monthly for access to exclusive Content.</p>

<p>Minimum Subscription Threshold:</p>

<ul>
<li>Creators must maintain a minimum Subscription Fee of $3 USD per month (or the equivalent in other currencies as determined by After Dark Audio). Subscriptions below this threshold are not permitted.</li>
</ul>

<p>Tiered Subscriptions:</p>

<ul>
<li>Creators may offer multiple subscription tiers with varying prices and access levels. Each tier must meet the $3 USD minimum.</li>
</ul>

<p>No Tipping or Gifting:</p>

<ul>
<li>After Dark Audio does not support tipping, gifting, or direct payments between Users. All payments must be processed through the Platform as subscription fees.</li>
</ul>

<h4>5.2 Subscription Fees and Auto-Renewal</h4>

<p>When you select a Subscription:</p>

<ol>
<li>You authorize After Dark Audio and our payment processors to charge your payment method the applicable Subscription Fee on the billing date you select (typically the same date each month);</li>
<li>Your Subscription will automatically renew at the current rate plus any applicable sales taxes, unless you cancel before the renewal date;</li>
<li>You authorize recurring charges to your selected payment method;</li>
<li>By subscribing, you acknowledge that you will receive no further notice regarding renewal charges unless the price increases, in which case you will receive notification and have the option to cancel before the new rate is charged;</li>
<li>If a payment is declined or fails, we will attempt to process the charge using your backup payment method if provided;</li>
<li>If you cancel your Subscription before the end of the current billing period, your access will continue until the end of that period, after which no further charges will be applied.</li>
</ol>

<h4>5.3 Subscription Cancellation and Refunds</h4>

<ul>
<li>You may cancel your Subscription at any time through your Account settings;</li>
<li>Cancellations take effect at the end of your current billing period;</li>
<li>Subscription fees are non-refundable, and no refunds will be issued for unused portions of your subscription period;</li>
<li>You will retain access to Content through the end of your paid billing period;</li>
<li>After cancellation, you will no longer have access to the Creator's exclusive Content.</li>
</ul>

<h4>5.4 Price Changes</h4>
<p>After Dark Audio or individual Creators may change Subscription Fees. If a Creator increases their Subscription Fee:</p>

<ul>
<li>Existing Subscribers will be notified at least 10 days before the increase takes effect;</li>
<li>You have the right to cancel your Subscription before the new rate is applied;</li>
<li>If you do not cancel, your continued subscription constitutes acceptance of the new price.</li>
</ul>

<h4>5.5 No Refunds for Chargebacks or Disputes</h4>

<p>You agree not to:</p>

<ul>
<li>File unjustified refund requests;</li>
<li>Dispute charges with your payment card provider or bank in bad faith;</li>
<li>File chargeback requests for legitimate Subscription fees or payments made through the Platform.</li>
</ul>

<p>If we determine that any refund or chargeback request was made in bad faith or fraudulently, we reserve the right to:</p>

<ul>
<li>Suspend or terminate your Account;</li>
<li>Forfeit any funds in your Account;</li>
<li>Prevent you from registering a new Account;</li>
<li>Report fraudulent activity to appropriate authorities and payment processors.</li>
</ul>

<hr />

<h3>6. CREATOR PAYOUTS AND PAYMENT PROCESSING</h3>

<h4>6.1 Payout Structure and Fees</h4>

<p>After Dark Audio charges a platform fee of 15% of all Subscriber payments. Creator Earnings are calculated as:</p>

<p>Creator Earnings = (Subscription Fees Received) – (15% Platform Fee) – (Payment Processing Fees) – (Applicable Taxes)</p>

<h4>6.2 Payment Processing</h4>

<ul>
<li>All Subscriber payments are processed by approved high-risk payment processors specializing in adult content;</li>
<li>Payments may be direct ACH transfers to your verified bank account, or processed through third-party payment platforms as determined by After Dark Audio;</li>
<li>Creator Earnings are calculated in USD;</li>
<li>We are not responsible for currency conversion fees charged by your bank or payment processor;</li>
<li>We do not control exchange rates, banking charges, or payment processor fees.</li>
</ul>

<h4>6.3 Bank Account Verification and Payout Methods</h4>

<p>To receive payouts, you must:</p>

<ol>
<li>Provide verified banking information (ACH transfer details or third-party payment platform credentials);</li>
<li>Complete age and identity verification as required;</li>
<li>Provide tax identification information as required by law (W-9 or equivalent);</li>
<li>Maintain valid, current banking information;</li>
<li>Comply with all anti-money laundering (AML) and Know Your Customer (KYC) requirements.</li>
</ol>

<p>Payout Schedule:</p>

<ul>
<li>Creator Earnings are typically calculated and processed monthly;</li>
<li>Minimum payout threshold: After a Creator generates a minimum of $20 USD in Creator Earnings, funds become available for withdrawal;</li>
<li>Payouts are processed within 5-10 business days after the end of the month in which earnings were generated;</li>
<li>Payment delays may occur due to payment processor processing times, bank processing, or verification requirements.</li>
</ul>

<h4>6.4 Chargebacks and Disputed Payments</h4>

<p>If a Subscriber successfully files a chargeback or requests a refund from their payment card provider:</p>

<ul>
<li>We may deduct the full Subscription Fee amount plus any chargeback fees (typically $15-$100) from your future Creator Earnings;</li>
<li>If your Account does not have sufficient funds to cover the chargeback and associated fees, your Account balance may go negative;</li>
<li>Negative balances must be resolved before future payouts are processed;</li>
<li>Excessive chargebacks may result in Account suspension or termination.</li>
</ul>

<h4>6.5 Tax Responsibility</h4>

<p>Creators are solely responsible for their own tax compliance and reporting:</p>

<ul>
<li>You are responsible for reporting all Creator Earnings to applicable tax authorities;</li>
<li>You warrant that you have reported and will report all Platform income as required by law;</li>
<li>After Dark Audio does not provide tax advice and is not responsible for your tax obligations;</li>
<li>You may be required to provide tax identification documents (W-9, 1099, or equivalent);</li>
<li>If you become tax non-compliant or are named in any tax investigation related to Platform activity, we reserve the right to suspend payouts, restrict your Account, or terminate your Account.</li>
</ul>

<hr />

<h3>7. GEOGRAPHIC RESTRICTIONS AND BLOCKING</h3>

<h4>7.1 Permitted Jurisdictions</h4>

<p>After Dark Audio is focused on serving North America and the European Union. However, we may permit Creators and Subscribers from other jurisdictions, provided they comply with all applicable local laws and these Terms.</p>

<h4>7.2 Blocked Jurisdictions</h4>

<p>Access to the Platform is blocked for users from the following regions, among others:</p>

<ul>
<li>China</li>
<li>Russia</li>
<li>Belarus</li>
<li>Iran</li>
<li>North Korea</li>
<li>Crimea and other sanctioned territories</li>
</ul>

<p>We may expand this list at any time to comply with sanctions, legal requirements, or risk management protocols.</p>

<h4>7.3 VPN and Proxy Detection</h4>

<p>After Dark Audio may employ technology to detect and block:</p>

<ul>
<li>Virtual Private Networks (VPNs) attempting to circumvent geographic restrictions;</li>
<li>Proxy servers;</li>
<li>Other tools used to obscure true location or evade geographic blocks.</li>
</ul>

<p>Attempting to circumvent geographic restrictions or age verification measures is a violation of these Terms and may result in Account suspension or termination.</p>

<h4>7.4 Jurisdictional Compliance</h4>

<p>All users must comply with the laws of their respective jurisdictions regarding:</p>

<ul>
<li>Age requirements for accessing adult content;</li>
<li>Content consumption and distribution restrictions;</li>
<li>Payment and financial transaction regulations;</li>
<li>Any other applicable local, state, or national laws.</li>
</ul>

<p>After Dark Audio is not responsible for ensuring your compliance with local laws. It is your responsibility to determine whether your access to and use of the Platform is legal in your jurisdiction.</p>

<hr />

<h3>8. ACCOUNT SUSPENSION, RESTRICTION, AND TERMINATION</h3>

<h4>8.1 Grounds for Suspension or Termination</h4>

<p>After Dark Audio may suspend, restrict, or terminate your Account or Content, pause payments, and/or withhold Creator Earnings at any time and for any reason, including but not limited to:</p>

<p>Serious Violations:</p>

<ul>
<li>Repeated or serious breaches of these Terms;</li>
<li>Uploading, distributing, or attempting to distribute prohibited Content;</li>
<li>Verification fraud or providing false information;</li>
<li>Age verification failure or underage access attempt;</li>
<li>Illegal activity or activity that violates laws in any jurisdiction where Content is accessed;</li>
<li>Chargeback fraud or frivolous payment disputes.</li>
</ul>

<p>Suspicious Activity:</p>

<ul>
<li>Suspected fraudulent, unlawful, or harmful activity;</li>
<li>Suspected money laundering or financial crimes;</li>
<li>Unauthorized account access or compromise;</li>
<li>Suspicious payment patterns or multiple payment failures;</li>
<li>Tax non-compliance or evasion.</li>
</ul>

<p>Risk Mitigation:</p>

<ul>
<li>Actions we believe pose risk to After Dark Audio, our payment processors, Subscribers, or the Platform;</li>
<li>Attempts to interfere with Platform security or functionality;</li>
<li>Harassment, threats, or attacks on other Users;</li>
<li>Spam, phishing, or malicious communications.</li>
</ul>

<p>Payment Processor Requirements:</p>

<ul>
<li>Violation of payment processor policies;</li>
<li>Payment processor deactivation or rejection of your account;</li>
<li>High chargeback rates or patterns.</li>
</ul>

<h4>8.2 Suspension Process</h4>

<p>When we suspend your Account:</p>

<ol>
<li>We may do so without prior warning for serious violations;</li>
<li>We will provide notice of the suspension and the reason when possible;</li>
<li>Your Account will be placed under review;</li>
<li>During suspension, all payments and payouts are paused;</li>
<li>You will not have access to your Account or Content;</li>
<li>Subscribers will not have access to your Content during the suspension period.</li>
</ol>

<h4>8.3 Termination Process</h4>

<p>If we determine that a violation has occurred and decide to terminate your Account:</p>

<ol>
<li>We will provide written notice of termination and the reasons;</li>
<li>Your Account will be permanently closed;</li>
<li>All Content will be removed from the Platform;</li>
<li>Any unpaid Creator Earnings may be forfeited;</li>
<li>All Subscriptions to your account will be cancelled;</li>
<li>You will no longer have access to your Account or any Content;</li>
<li>Subscribers will receive notice that your account has been terminated.</li>
</ol>

<h4>8.4 Creator Earnings Forfeiture</h4>

<p>If we terminate your Account due to serious violations, fraud, illegal activity, or payment processor requirements, we may:</p>

<ul>
<li>Forfeit all or any portion of your unpaid Creator Earnings;</li>
<li>Retain forfeited funds to offset damages, legal costs, or payment processor losses;</li>
<li>Report your activity to law enforcement or relevant authorities.</li>
</ul>

<h4>8.5 Appeal Process</h4>

<p>If your Account is suspended or terminated, you may submit a written appeal within 30 days:</p>

<p>Appeal Process:</p>

<ol>
<li>Send a detailed appeal letter to our support email ([support@afterdarkaudio.com] or similar);</li>
<li>Include your Account information, username, and the reason for suspension/termination;</li>
<li>Explain your position, provide evidence supporting your appeal, and describe corrective actions you have taken;</li>
<li>Write in a professional, factual tone without emotional language or threats;</li>
<li>Our review team will evaluate your appeal within 14-30 days;</li>
<li>We will notify you of the appeal decision by email;</li>
<li>If your appeal is denied, you may not file another appeal for the same violation.</li>
</ol>

<p>Appeal Limitations:</p>

<ul>
<li>Appeals are at our sole discretion;</li>
<li>We are not obligated to overturn our decisions;</li>
<li>Some violations (CSAM, extreme abuse) are not subject to appeal;</li>
<li>If you do not appeal within 30 days, your right to appeal is waived;</li>
<li>Abusive, threatening, or harassing appeal communications will result in forfeiture of appeal rights.</li>
</ul>

<h4>8.6 Voluntary Account Deletion</h4>

<p>You may delete your Account at any time through your Account settings:</p>

<ul>
<li>For Subscribers: Your Account will be deleted within a reasonable time frame.  No further charges will be applied. You will lose access to all Content and your Account.</li>
<li>For Creators: Your Account will remain active until all active Subscriptions expire.  We will then process final payouts and delete your Account. All Content will be removed, and Subscriptions cannot be renewed. You will lose access to your Account and all Content.</li>
</ul>
<p>After deletion, we may retain your data in accordance with our Privacy Policy and applicable law.</p>

<hr />

<h3>9. INTELLECTUAL PROPERTY RIGHTS</h3>

<h4>9.1 Platform Ownership</h4>

<p>After Dark Audio retains all rights in and to:</p>

<ul>
<li>The Platform infrastructure, design, and functionality;</li>
<li>All proprietary software, code, and technology;</li>
<li>All trademarks, logos, and branding associated with After Dark Audio;</li>
<li>All anonymized data relating to Platform use and user activity.</li>
</ul>

<p>Except as expressly granted in these Terms, you have no rights to the Platform or any intellectual property associated with it.</p>

<h4>9.2 Creator Content License</h4>

<p>By uploading Content to the Platform, you grant After Dark Audio a limited license to:</p>

<ul>
<li>Store, display, and distribute your Content on the Platform;</li>
<li>Watermark or include branding on your Content;</li>
<li>Use your Content to improve Platform features and functionality;</li>
<li>Use your Content for internal analytics and improvement purposes;</li>
<li>Include your Content in aggregated data or statistics (without identifying you).</li>
</ul>

<p>This license is:</p>

<ul>
<li>Non-exclusive;</li>
<li>Worldwide;</li>
<li>Royalty-free;</li>
<li>Non-transferable (we cannot sublicense to third parties without permission);</li>
<li>Limited to the operation of the Platform;</li>
<li>Subject to termination upon Account deletion.</li>
</ul>

<h4>9.3 Moral Rights</h4>

<p>To the extent permitted by law, you waive any moral rights in your Content, including rights of attribution and integrity.</p>

<h4>9.4 Content Ownership After Termination</h4>

<p>After Dark Audio will never sell your Content to other platforms. However:</p>

<ul>
<li>If our company or assets are sold or acquired, we may transfer your granted license to the acquiring entity;</li>
<li>After Account termination, we will make reasonable efforts to remove your Content within 30 days;</li>
<li>Backups and cached copies may persist for technical or legal reasons;</li>
<li>We are not responsible for Content that has been downloaded or redistributed by third parties before removal.</li>
</ul>

<h4>9.5 Third-Party Content in Creator Content</h4>

<p>You warrant that any third-party material in your Content (music, sound effects, voice actors, etc.) is properly licensed:</p>

<ul>
<li>You are responsible for obtaining licenses from copyright holders;</li>
<li>You are responsible for paying associated royalties or licensing fees;</li>
<li>After Dark Audio is not responsible for collecting or managing third-party royalties;</li>
<li>Failure to properly license third-party content may result in DMCA takedown and Content removal.</li>
</ul>

<hr />

<h3>10. DIGITAL MILLENNIUM COPYRIGHT ACT (DMCA) AND INTELLECTUAL PROPERTY ENFORCEMENT</h3>

<h4>10.1 Copyright and DMCA Compliance</h4>

<p>After Dark Audio respects intellectual property rights and complies with the Digital Millennium Copyright Act (DMCA). We maintain a DMCA agent and process takedown notices in accordance with the law.</p>

<p>DMCA Agent:</p>

<ul>
<li>Name: [Legal Compliance Department]</li>
<li>Address: [After Dark Audio, Salt Lake City, Utah]</li>
<li>Email: dmca@afterdarkaudio.com</li>
</ul>

<h4>10.2 Submitting DMCA Takedown Notices</h4>

<p>If you believe your copyrighted work is being infringed on the Platform:</p>

<ol>
<li>Notice Requirements: Submit a written notice (electronic or physical mail) that includes:
<ul>
<li>Your name, address, phone number, and email address;</li>
<li>Identification of the copyrighted work(s) being infringed;</li>
<li>Identification of the infringing material and its location on the Platform;</li>
<li>A statement that you have a good faith belief the use is infringing and unauthorized;</li>
<li>A statement, under penalty of perjury, that you are authorized to act on behalf of the copyright holder;</li>
<li>Your physical or electronic signature.</li>
</ul>
</li>
<li>Submission: Send the notice to our DMCA Agent at the contact information above.
<li>Response Time: We will investigate and respond within 10-14 business days.</li>
<li>Content Removal: If the notice is valid, we will remove the infringing Content and notify the Creator.</li>
</ol>

<h4>10.3 Counter-Notification</h4>

<p>If your Content has been removed due to a DMCA notice and you believe the removal was erroneous:</p>

<ol>
<li>Counter-Notification Requirements: Submit a written counter-notification that includes:
<ul>
<li>Your name, address, phone number, and email address;</li>
<li>Identification of the removed Content and where it was located;</li>
<li>A statement that you have a good faith belief the removal was mistaken;</li>
<li>A statement that you consent to jurisdiction of federal court in your district;</li>
<li>Your physical or electronic signature.</li>
</ul>
</li>
<li>Submission: Send to our DMCA Agent.</li>
<li>Response Time: We will evaluate and respond within 10-14 business days.</li>
<li>Re-instatement: If the counter-notification is valid, we may reinstate the Content.</li>
</ol>

<h4>10.4 Repeat Infringement Policy</h4>

<p>Users who are found to have engaged in repeated copyright infringement will:</p>

<ul>
<li>Receive a formal warning for the first violation;</li>
<li>Face Content removal and Account restrictions for the second violation;</li>
<li>Have their Account terminated for three or more violations;</li>
<li>Be permanently banned from the Platform.</li>
</ul>

<h4>10.5 Other Intellectual Property Claims</h4>

<p>If you believe your trademark, trade secret, patent, publicity right, or other intellectual property has been infringed on the Platform:</p>

<ul>
<li>Submit a detailed complaint to legal@afterdarkaudio.com;</li>
<li>Include proof of your intellectual property right (registration certificates, etc.);</li>
<li>Describe the infringing content and its location;</li>
<li>We will review and respond within 14-30 business days at our discretion.</li>
</ul>

<hr />

<h3>11. CONTENT MODERATION AND REPORTING</h3>

<h4>11.1 Reporting Violations</h4>

<p>If you discover Content or conduct that violates these Terms or applicable law, you may report it:</p>

<ul>
<li>Report Button: Use the report function directly on the Content;</li>
<li>Email: report@afterdarkaudio.com;</li>
<li>Support Form: Submit a detailed report via our Platform support system.</li>
</ul>

<p>Reports should include:</p>

<ul>
<li>Specific Content or user involved;</li>
<li>Description of the violation;</li>
<li>Links or identifying information;</li>
<li>Any supporting evidence or documentation.</li>
</ul>

<h4>11.2 Investigation Process</h4>

<p>Upon receiving a report:</p>

<ol>
<li>We will review the reported Content or conduct;</li>
<li>We will investigate whether the Terms have been violated;</li>
<li>Investigation typically takes 3-7 business days;</li>
<li>We will take appropriate action, which may include:
<ul>
<li>Content removal;</li>
<li>Account warning;</li>
<li>Temporary suspension;</li>
<li>Permanent termination;</li>
<li>Report to law enforcement (if illegal activity is suspected).</li>
</ul>
</li>
</ol>
<h4>11.3 Investigation Limitations</h4>

<ul>
<li>We are not responsible for Content we do not actively monitor or have not been made aware of;</li>
<li>We do not pre-screen all Content;</li>
<li>We use technology tools, AI, and human review to identify violations;</li>
<li>We reserve the right to enforce these Terms at our discretion;</li>
<li>We are not liable for delayed action on reports.</li>
</ul>

<h4>11.4 No Obligation to Monitor</h4>

<p>We are not required to monitor Content actively. Users should report violations, but failure to report or delayed reporting does not mean we have endorsed or approved any Content.</p>

<hr />

<h3>12. PROHIBITED CONDUCT</h3>

<p>In addition to content restrictions, you agree not to:</p>

<p>Account and Access:</p>

<ul>
<li>Create multiple Accounts to evade restrictions or bypass suspension/termination;</li>
<li>Share your Account credentials with third parties;</li>
<li>Attempt to gain unauthorized access to other Accounts or the Platform;</li>
<li>Impersonate another user, Creator, or employee of After Dark Audio;</li>
<li>Scrape, crawl, or automatically download Platform data;</li>
<li>Interfere with Platform security or functionality.</li>
</ul>

<p>Communications:</p>

<ul>
<li>Send unsolicited advertising, spam, or promotional messages;</li>
<li>Harass, threaten, bully, or defame other Users;</li>
<li>Send phishing emails or malicious links;</li>
<li>Blackmail, extort, or threaten other Users;</li>
<li>Doxx other Users or share private information without consent;</li>
<li>Engage in fraudulent schemes or scams.</li>
</ul>

<p>Technical:</p>

<ul>
<li>Upload viruses, malware, or harmful code;</li>
<li>Disable, interfere with, or circumvent security features;</li>
<li>Reverse engineer or attempt to decompile Platform code;</li>
<li>Resell, redistribute, or exploit Platform access;</li>
<li>Use bots, scrapers, or automated tools without permission.</li>
</ul>

<p>Financial Fraud:</p>

<ul>
<li>Use stolen payment methods or fraudulent payment information;</li>
<li>Conduct money laundering or financial crimes;</li>
<li>Engage in chargebacks or false payment disputes in bad faith;</li>
<li>Structure transactions to evade monitoring or reporting;</li>
<li>Facilitate payment fraud or unauthorized transactions.</li>
</ul>

<p>Rights Violations:</p>

<ul>
<li>Violate anyone's intellectual property, privacy, or publicity rights;</li>
<li>Post non-consensual intimate imagery;</li>
<li>Create deepfakes or manipulated Content without consent;</li>
<li>Stalk or engage in coordinated harassment campaigns.</li>
</ul>

<hr />

<h3>13. DISCLAIMERS AND LIMITATIONS OF LIABILITY</h3>

<h4>13.1 "As-Is" Disclaimer</h4>

<p>THE PLATFORM IS PROVIDED ON AN "AS-IS" AND "AS AVAILABLE" BASIS. After Dark Audio makes no warranties, express or implied, regarding:</p>

<ul>
<li>The Platform's functionality, availability, or compatibility with your devices;</li>
<li>The accuracy, completeness, or reliability of any Content;</li>
<li>That the Platform will be error-free, secure, or uninterrupted;</li>
<li>That any defects will be corrected;</li>
<li>That the Platform complies with any particular laws or regulations;</li>
<li>Any claims made by other Users or Creators.</li>
</ul>

<h4>13.2 No Specific Promises</h4>

<p>We do not promise that:</p>

<ul>
<li>Any Creator will generate income or reach any earnings threshold;</li>
<li>Any Subscriber will have any particular experience or access to Content;</li>
<li>Your Account will never be suspended or terminated;</li>
<li>Content will always be available or will not be removed;</li>
<li>We will enforce these Terms uniformly or consistently;</li>
<li>The Platform will meet your specific needs or expectations.</li>
</ul>

<h4>13.3 Limitation of Liability</h4>

<p>To the maximum extent permitted by law:</p>

<ul>
<li>After Dark Audio is not liable for any indirect, incidental, special, consequential, punitive, or exemplary damages;</li>
<li>We are not liable for loss of profit, revenue, data, goodwill, business opportunity, or reputation;</li>
<li>We are not liable for suspension, termination, payment withholding, or Content removal;</li>
<li>We are not liable for any third-party conduct, Content, or disputes;</li>
<li>We are not liable for service interruptions, outages, or technical failures;</li>
<li>We are not liable for chargebacks, payment processor actions, or banking errors;</li>
<li>We are not liable for the unauthorized access or compromise of your Account;</li>
<li>Our total liability to you for all claims shall not exceed the greater of: (a) 100% of fees you paid to us in the past 12 months, or (b) $100 USD.</li>
</ul>

<h4>13.4 Disclaimer of Warranties for Third Parties</h4>

<p>We are not responsible for and do not endorse:</p>

<ul>
<li>Any Content posted by Users;</li>
<li>Any conduct, statements, or actions of Creators or Subscribers;</li>
<li>Any external links or third-party websites;</li>
<li>Any representations or promises made by other Users;</li>
<li>Any disputes between Users;</li>
<li>Any goods or services offered through or promoted on the Platform.</li>
</ul>

<hr />

<h3>14. INDEMNIFICATION</h3>

<p>You agree to indemnify, defend, and hold harmless After Dark Audio, its officers, employees, agents, and affiliates from any and all claims, damages, losses, costs, and expenses (including attorney's fees) arising from or related to:</p>

<ul>
<li>Your use of the Platform;</li>
<li>Your breach of these Terms or any incorporated policy;</li>
<li>Your violation of applicable law;</li>
<li>Your Content or conduct;</li>
<li>Your infringement of any third-party rights;</li>
<li>Any dispute between you and another User;</li>
<li>Any financial fraud, chargebacks, or payment disputes involving your Account;</li>
<li>Any unauthorized use of your Account by third parties;</li>
<li>Any claims arising from your access to or use of third-party links or websites.</li>
</ul>

<hr />

<h3>15. DISPUTE RESOLUTION AND ARBITRATION</h3>

<h4>15.1 Governing Law</h4>

<p>These Terms and your use of the Platform are governed by the laws of the State of Utah, without regard to conflict of law principles. The application of the United Nations Convention on Contracts for the International Sale of Goods is explicitly excluded.</p>

<h4>15.2 Informal Resolution</h4>

<p>Before initiating any formal dispute resolution process, you agree to:</p>

<ol>
<li>Notify After Dark Audio in writing of the dispute;</li>
<li>Provide detailed information about the claim;</li>
<li>Attempt to resolve the matter through good-faith negotiation within 30 days;</li>
<li>Contact: disputes@afterdarkaudio.com.</li>
</ol>

<p>If the dispute is not resolved within 30 days, either party may proceed to arbitration or litigation as provided below.</p>

<h4>15.3 Arbitration Agreement</h4>

<p>Except as expressly excluded below, you agree that any dispute, claim, or controversy arising from or relating to these Terms or your use of the Platform shall be resolved by binding arbitration rather than in court.</p>

<p>Arbitration Details:</p>

<ul>
<li>Arbitrator: A single neutral arbitrator selected in accordance with JAMS Comprehensive Arbitration &amp; Mediation Services (JAMS) rules;</li>
<li>Location: Arbitration will be conducted in Salt Lake City, Utah (or by remote proceedings);</li>
<li>Rules: JAMS Streamlined Arbitration Rules and Procedures;</li>
<li>Costs: After Dark Audio will pay arbitrator fees and administrative costs. You will pay your own attorney's fees (if any) and costs;</li>
<li>Discovery: Limited discovery as permitted by arbitration rules;</li>
<li>Award: The arbitrator's award is final and binding and may be entered in any court of competent jurisdiction;</li>
<li>Class Action Waiver: You agree that arbitration is conducted on an individual basis only. You waive the right to bring a class action or represent other parties in arbitration.</li>
</ul>

<p>Exceptions to Arbitration:</p>

<p>The following disputes may be brought in court rather than arbitration:</p>

<ul>
<li>Claims for intellectual property infringement;</li>
<li>Injunctive relief to prevent platform abuse or violation of these Terms;</li>
<li>Disputes arising from payment processor actions or banking issues;</li>
<li>Claims within the jurisdictional limits of small claims court (where available and you elect small claims court).</li>
</ul>

<h4>15.4 Litigation</h4>

<p>For disputes not subject to arbitration, you agree to exclusive jurisdiction in the state and federal courts located in Salt Lake City, Utah. You waive any objection to venue or inconvenient forum.</p>

<h4>15.5 Limitation Period for Claims</h4>

<p>Any claim must be filed within one (1) year of when the claim arose or when you discovered or should have discovered it. Claims filed after this period are barred.</p>

<hr />

<h3>16. MODIFICATIONS TO TERMS</h3>

<p>After Dark Audio may modify these Terms at any time by posting the updated Terms on the Platform or by sending notice via email. Material changes will be communicated at least 10 days before taking effect.</p>

<p>Your continued use of the Platform after modification constitutes your acceptance of the modified Terms. If you do not accept modifications, you must discontinue use of the Platform and delete your Account.</p>

<hr />

<h3>17. TERMINATION AND SURVIVAL</h3>

<h4>17.1 Termination Authority</h4>

<p>These Terms take effect when you register and continue until terminated by:</p>

<ul>
<li>You, by deleting your Account at any time;</li>
<li>After Dark Audio, by suspension or termination of your Account for violation of these Terms.</li>
</ul>

<h4>17.2 Survival</h4>

<p>The following sections survive termination:</p>

<ul>
<li>Section 3 (Eligibility and Registration);</li>
<li>Section 9 (Intellectual Property Rights);</li>
<li>Section 10 (DMCA and IP Enforcement);</li>
<li>Section 13 (Disclaimers and Limitations of Liability);</li>
<li>Section 14 (Indemnification);</li>
<li>Section 15 (Dispute Resolution and Arbitration);</li>
<li>Section 16 (Modifications to Terms);</li>
<li>Section 18 (Miscellaneous).</li>
</ul>

<hr />

<h3>18. MISCELLANEOUS</h3>

<h4>18.1 Entire Agreement</h4>

<p>These Terms constitute the entire agreement between you and After Dark Audio regarding your use of the Platform and supersede all prior agreements, communications, or understandings.</p>

<h4>18.2 Severability</h4>

<p>If any provision of these Terms is found to be unenforceable, that provision will be modified to the minimum extent necessary to make it enforceable, or if not possible, severed. The remaining Terms will continue in full force and effect.</p>

<h4>18.3 Waiver</h4>

<p>Failure by After Dark Audio to enforce any provision of these Terms does not constitute a waiver of that provision or any other provision.</p>

<h4>18.4 Assignment</h4>

<p>You may not assign, transfer, or delegate your rights or obligations under these Terms. After Dark Audio may assign these Terms and all associated rights and obligations at any time without notice.</p>

<h4>18.5 Third-Party Beneficiaries</h4>

<p>These Terms do not create any third-party beneficiaries. Only you and After Dark Audio are parties to this agreement.</p>

<h4>18.6 Notices</h4>

<p>Notices to After Dark Audio should be sent to:</p>

<address>
After Dark Audio​<br />
Entek Solutions, LLC​<br />
[Address to be completed]​<br />
Email: legal@afterdarkaudio.com
</address>

<p>Notices to you will be sent via email or posted on your Account.</p>

<h4>18.7 Force Majeure</h4>

<p>After Dark Audio is not liable for failure to perform obligations under these Terms due to events beyond our reasonable control, including acts of God, natural disasters, wars, pandemics, government actions, or internet infrastructure failures.</p>

<h4>18.8 Relationship of Parties</h4>

<p>Nothing in these Terms creates an agency, partnership, joint venture, franchise, or employment relationship between you and After Dark Audio.</p>

<h4>18.9 No Agency</h4>

<p>You have no authority to bind After Dark Audio or represent us to any third party.</p>

<h4>18.10 Counterparts</h4>

<p>These Terms may be executed in counterparts, each of which constitutes an original and all of which together constitute one agreement.</p>

<hr />

<h3>19. CONTACT INFORMATION</h3>

<p>For questions, complaints, or inquiries regarding these Terms of Service, please contact:</p>

<p>After Dark Audio​ Customer Support:</p>

<p>support@afterdarkaudio.com</p>

<p>Legal Inquiries:</p>

<p>legal@afterdarkaudio.com</p>

<p>DMCA Notices:</p>

<p>dmca@afterdarkaudio.com</p>

<p>Dispute Resolution:</p>

<p>disputes@afterdarkaudio.com</p>

<p>Appeals:</p>

<p>appeals@afterdarkaudio.com</p>

<hr />

<h3>20. FINAL NOTICE REGARDING ADULT CONTENT</h3>

<p>After Dark Audio is a platform for adult audio content. By using this Platform, you acknowledge:</p>

<ul>
<li>You are at least 18 years old and have completed age verification;</li>
<li>You have read and understood these Terms;</li>
<li>You understand that some Content is sexually explicit and intended for adults;</li>
<li>You accept responsibility for your own usage and compliance with local laws;</li>
<li>You waive any claims related to exposure to adult content;</li>
<li>You acknowledge that After Dark Audio takes reasonable but not absolute measures to prevent minors from accessing adult content;</li>
<li>You will report suspected underage access or violations immediately.</li>
</ul>

<h3>ACKNOWLEDGMENT</h3>

<p>BY CLICKING "I AGREE" OR BY ACCESSING AND USING THE PLATFORM, YOU ACKNOWLEDGE THAT:</p>

<ol>
<li>You have read these Terms of Service in their entirety;</li>
<li>You understand all provisions, including the arbitration agreement and liability limitations;</li>
<li>You agree to be bound by these Terms;</li>
<li>You are at least 18 years of age and have completed age verification;</li>
<li>You comply with all applicable laws in your jurisdiction.</li>
</ol>

<hr />

<p><em>This Terms of Service is a legal document. Please review it carefully. For legal advice regarding your specific situation, please consult with an attorney licensed in your jurisdiction.</em></p>

EOT;

        }

        return $data;
    }
}
