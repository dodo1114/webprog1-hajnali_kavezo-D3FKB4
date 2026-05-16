<?php

session_destroy();
session_start();
flash('success', 'Sikeres kijelentkezés.');
redirect_to('fooldal');

