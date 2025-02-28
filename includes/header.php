<div class="site-navbar py-2" style="background: linear-gradient(to right, white,grey);">

    <div class="search-wrap">
        <div class="container">
            <a href="#" class="search-close js-search-close"><span class="icon-close2"></span></a>
            <form action="store.php" method="GET">
                <input type="text" name="search" class="form-control" placeholder="Search keyword and hit enter...">
            </form>
        </div>
    </div>
    <div class="container">
        <div class="d-flex align-items-center justify-content-between">
            <div class="logo">
                <div class="site-logo">
                    <h3 style="display: inline-block; color:red;">CURENET</h3>
                    <a href="index.php" class="js-logo-clone"><img src="images/favicon.png" style="width:130px; height: 70px;"></a>
                </div>
            </div>
            <div class="main-nav d-none d-lg-block">
                <nav class="site-navigation text-right text-md-center" role="navigation">
                    <ul class="site-menu js-clone-nav d-none d-lg-block">
                        <li><a href="index.php">Home</a></li>
                        <li><a href="store.php?store=all">Medicines</a></li>
                        <li class="has-children">
                            <a href="#">Categories</a>
                            <ul class="dropdown">
                                <li><a href="store.php?cat=medicine">Skin Care</a></li>
                                <li><a href="store.php?cat=self-care">Pain Relief</a></li>
                                <li><a href="store.php?cat=medicine">Digestive Health</a></li>
                                <li><a href="store.php?cat=medicine">Vitamins</a></li>
                                <li><a href="store.php?cat=medicine">flu-like symptoms</a></li>
                            </ul>
                        </li>
                        <li><a href="about.php">About</a></li>
                    </ul>
                </nav>
            </div>
            <div class="icons">
                <a href="#" class="icons-btn d-inline-block js-search-open"><span class="icon-search"></span></a>
                <a href="cart.php" class="icons-btn d-inline-block bag">
                    <span class="icon-shopping-bag"></span>
                    <?php
                    if (!empty($_SESSION['cart'])) {
                    ?>
                        <span class="number">
                            <?php echo sizeof($_SESSION['cart']); ?>
                        </span>
                    <?php
                    } else {
                    ?>
                        <span class="number">0</span>
                    <?php
                    }
                    ?>
                </a>
                <?php
                if (!isset($_SESSION['user_id'])) {
                ?>
                    <div class="user-menu">
                        <img src='images/user.png' style='background-color: black; padding:3px; margin-left:7px;'>
                        <div class="dropdown-menu">
                            <a href="login.php">Login</a>
                        </div>
                    </div>
                <?php
                } else {
                    $check_user_id = check_user($_SESSION['user_id']);
                    if ($check_user_id == 1) {
                ?>
                        <div class="user-menu">
                            <img src='images/user.png' style='background-color: black; padding:3px; margin-left:7px;'>
                            <div class="dropdown-menu">
                                <a href="profile.php">Profile</a>
                                <a href="logout.php">Logout</a>
                            </div>
                        </div>
                <?php
                    } else {
                        post_redirect("logout.php");
                    }
                }
                ?>
                <a href="" class="site-menu-toggle js-menu-toggle ml-3 d-inline-block d-lg-none"><span class="icon-menu"></span></a>
            </div>
        </div>
    </div>
</div>

<style>
    .user-menu {
        position: relative;
        display: inline-block;
    }

    .user-menu img {
        cursor: pointer;
    }

    .dropdown-menu {
        display: none;
        position: absolute;
        background-color: white;
        box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.2);
        z-index: 1;
        right: 0;
        min-width: 100px;
        border-radius: 5px;
        overflow: hidden;
    }

    .dropdown-menu a {
        color: black;
        padding: 12px 16px;
        text-decoration: none;
        display: block;
    }

    .dropdown-menu a:hover {
        background-color: #f1f1f1;
    }

    .user-menu:hover .dropdown-menu {
        display: block;
    }
</style>