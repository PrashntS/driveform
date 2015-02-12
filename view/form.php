<?php
namespace DriveForm\View\Form;
include dirname(__FILE__).'/commons.php';

function init() {
    \DriveForm\View\head();
    ?>
    <body>
        <form action="/php/register" method="POST" enctype="multipart/form-data">
            <section id="one">
                <div>
                    <a href="http://autonomi.ducic.ac.in" target="_blank"><img src="img/autonomi.png" class="logo"></a>
                    <a href="http://pattern.ducic.ac.in" target="_blank"><img src="img/lpe.png" class="logo"></a>
                    <h2>Please choose the Workshop you wish to register into</h2>
                    <ul class="inline lg" data-type="select">
                        <li data-workshop="3D1" data-type="slot">
                            <img src="img/128.png">
                        </li>
                        <li data-workshop="3D2" data-type="slot">
                            <img src="img/130.png">
                        </li>
                        <li data-workshop="RB1" data-type="slot">
                            <img src="img/129.png">
                        </li>
                        <li data-workshop="RB2" data-type="slot">
                            <img src="img/132.png">
                        </li>
                    </ul><br>
                    <span id="notice"></span><br>
                    <input type="text" name="Workshop" data-hidden="false">
                </div>
            </section>
            <section id="two">
                <div>
                    <h2>What is your Name?</h2>
                    <input type="text" name="Name" placeholder="Nico Hades" data-tab="three">
                    <div data-type="nav" data-target="three">NEXT</div>
                    <div data-type="nav" data-target="one">PREVIOUS</div>
                </div>
            </section>
            <section id="three">
                <div>
                    <h2>How do we contact you?</h2>
                    <span>Email ID</span>
                    <input type="email" name="Email" placeholder="leo@valdez.com">
                    <span>Contact Number</span>
                    <input type="tel" name="Contact" placeholder="999 999 9999" data-tab="four">
                    <div data-type="nav" data-target="four">NEXT</div>
                    <div data-type="nav" data-target="two">PREVIOUS</div>
                </div>
            </section>
            <section id="four">
                <div>
                    <h2>What is your Institution/College?</h2>
                    <span>College</span>
                    <input type="text" name="College" placeholder="Camp Half Blood">
                    <span>Course/Specialization</span>
                    <input type="text" name="Course" placeholder="Sorcerer" data-tab="five">
                    <div data-type="nav" data-target="five">NEXT</div>
                    <div data-type="nav" data-target="three">PREVIOUS</div>
                </div>
            </section>
            <section id="five">
                <div>
                    <h2>Please give us the Payment Details</h2>
                    <span>Upload a Picture/Scan of the DD. (Only jpg, png, and gif files accepted. Max. Size = 7 MB.</span><br>

                    <input type="file" name="DD_Img" accept="image/*">

                    <input type="text" name="DD" placeholder="Demand Draft Number">
                    <input type="text" name="Bank" placeholder="Issuing Bank">
                    <div data-type="nav" data-target="four">PREVIOUS</div>
                    <button type="submit" data-target="three">SUBMIT</button>

                </div>
            </section>
        </form>
        <script type="text/javascript" src="js/jquery-min.js"></script>
        <script type="text/javascript" src="js/app.js"></script>
    </body>
    </html>
    <?php
}

function error($err_list) {
    \DriveForm\View\head();
    ?>
    <body>
        <section id="six" class="error">
            <div>
                <h2>We're Sorry.<br>There was an Error.</h2>
                <?php
                if (in_array('Server_Error', $err_list)) {
                    ?>
                    <span>We're experiencing a Some technical problems. Please try again later.</span>
                    <?
                } elseif (in_array('Workshop', $err_list)) {
                    ?>
                    <span>The chosen slot is not accepting any more entries, as all the seats have been registered.</span>
                    <?
                } else {
                    ?>
                    <span>Following entries were either missing, or contained invalid data:</span>
                    <ul>
                    <?php
                    if (in_array('Name', $err_list)) echo "<li>Name</li>";
                    if (in_array('Email', $err_list) || in_array('Email_Exists', $err_list)) echo "<li>Email ID</li>";
                    if (in_array('Contact', $err_list)) echo "<li>Contact Number</li>";
                    if (in_array('College', $err_list)) echo "<li>College/Institution Name</li>";
                    if (in_array('Course', $err_list)) echo "<li>Course/Specialization</li>";
                    if (in_array('DD_Img', $err_list)) echo "<li>Uploaded Image</li>";
                    if (in_array('DD', $err_list)) echo "<li>DD Number</li>";
                    if (in_array('Bank', $err_list)) echo "<li>Bank Name</li>";
                    ?>
                    </ul>
                    <?php
                }
                ?>
                <span>Your Registration is NOT ACCEPTED yet. You can Retry with correct data via following link.</span><br>
                <span>You can also contact us through our <a href="mail-to:autonomi@ducic.ac.in">Email</a>, or <a href="https://facebook.com/autonomi.cic">Facebook</a> handle, in case you need any kind of help.</span><br>
                <br>
                <button type="submit" data-target="three">RETRY</button>
            </div>
        </section>
        <script type="text/javascript" src="js/jquery-min.js"></script>
        <script type="text/javascript" src="js/app.js"></script>
    </body>
    </html>
    <?php
}

function success($reg_id) {
    \DriveForm\View\head();
    ?>
    <body>
    <section id="six" class="success">
        <div>
            <h2>Success!<br>Your Registration has been Accepted.</h2>
            <h3>Registration ID: #<?php echo $reg_id; ?></h3>
            <p>Here's what's going to happen next:</p>
            <ol>
                <li>We'll Email you an acknowledgment letter within 5 minutes of the receipt this form.</li>
                <li>We'll Review your registration and confirm the alloted slot, within the next 72 hours.</li>
            </ol>

            <span>In any case, if you notice that the information provided by you is incorrect, you can contact us on our <a href="mail-to:autonomi@ducic.ac.in">Email</a>, or the <a href="https://facebook.com/autonomi.cic">Facebook</a> handle.</span>
        </div>
    </section>
    </body>
    </html>
    <?php
}

?>