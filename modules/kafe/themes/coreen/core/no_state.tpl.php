<?php

echo('<p><strong>' . $this->t('{core:no_state:suggestions}') . '</strong></p>');
echo('<ul class="bul-dot">');
echo('<li>' . $this->t('{core:no_state:suggestion_goback}') . '</li>');
echo('<li>' . $this->t('{core:no_state:suggestion_closebrowser}') . '</li>');
echo('</ul>');

echo('<p><strong>' . $this->t('{core:no_state:causes}') . '</strong></p>');
echo('<ul class="bul-dot">');
echo('<li>' . $this->t('{core:no_state:cause_backforward}') . '</li>');
echo('<li>' . $this->t('{core:no_state:cause_openbrowser}') . '</li>');
echo('<li>' . $this->t('{core:no_state:cause_nocookie}') . '</li>');
echo('</ul>');

