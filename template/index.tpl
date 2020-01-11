<html>
<head>
    <title>Tab test</title> 
    <link rel="stylesheet" type="text/css" href="css/index.css">
    <meta charset="utf8">
    <meta name="viewport" content="width=500">
    {IF:{SITE_EDITING}}<meta http-equiv="refresh" content="{EXPIRE_SEC};url={ACTUAL_LINK}">{END_IF}
</head>
<body>
    <header>
        <div class="rounded">
            <h1>TT</h1>
            <h2>TAB TEST</h2>
        </div>
    </header>
    <div class="content">
        <div class="tabs">
            <div class="min-tabs">
                <h3 class="{REALTY_LIST_CLASS}">
                    <a href="/">Zoznam nehnuteľností</a> 
                </h3>
                <h3 class="{COMP_LIST_CLASS}">
                    <a href="/?computer">Zoznam počítačov</a>
                </h3>
                <h3 class="{USER_LIST_CLASS}">
                    <a href="/?user">Zoznam používateľov</a>
                </h3>
            </div>
        </div>
        <div class="pagination">
            <div class="first"><a href="{FIRST_SITE_LINK}" title="Prvá stránka">❮❮</a></div>
            <div class="prev"><a href="{PREV_SITE_LINK}" title="Predošlá stránka">❮</a></div>
            <div class="page">
                <form action="/" method="GET">
                    <input type="number" name="site" value="{ACT_SITE}" min="1" max="{MAX_SITE}" step="1" title="Aktuálna stránka"> / <span title="Počet stránok">{MAX_SITE}</span>
                    {IF:{OTHER_TABLE}}<input type="hidden" name="{TABLE}" value="">{END_IF}
                </form>
            </div>
            <div class="last"><a href="{LAST_SITE_LINK}" title="Posledná stránka">❯❯</a></div>
            <div class="next"><a href="{NEXT_SITE_LINK}" title="Ďalšia stránka">❯</a></div>
        </div>
        <form action="/do/edit.php" method="GET">
            <table cellspacing="0">
                <thead>
                    <tr>
                        <td>ID</td>
                        <td>Názov</td>
                        <td>Popis</td>
                        <td></td>
                    </tr>
                </thead>
                <tbody>
                    {CYCLE:{RESULT_TABLE}}
                    <tr class="{IF:{ITEM_EDIT} AND {SESSION_EDIT} IS {ITEM_ID}}editing me{ELSEIF:{ITEM_EDIT}}editing other{END_IF}">
                        <td>
                            <a name="item{ITEM_ID}" class="flag"></a>
                            {ITEM_ID}
                        </td>
                        <td>{ITEM_NAME}</td>
                        <td>
                            {IF:{ITEM_EDIT} AND {SESSION_EDIT} IS {ITEM_ID}}
                                <input type="hidden" name="id" value="{ITEM_ID}">
                                <input type="hidden" name="{TABLE}" value="">
                                <textarea name="desc">{ITEM_DESC}</textarea>
                                <div class="expire">
                                    <div style="width:{EXPIRE_PERCENT}%; animation-duration:{EXPIRE_SEC}s;"></div>
                                </div>
                            {ELSE}
                                {ITEM_DESC}
                            {END_IF} 
                        </td>
                        <td>
                            {IF:{ITEM_EDIT} AND {SESSION_EDIT} IS {ITEM_ID}}
                                <span title="Ja upravujem popis"><img src="img/edit_ok.png" alt=""></span>
                                <button type="submit" title="Potvrdiť úpravu popisu"><img src="img/accept.png" alt=""></button>
                                <a href="do/edit.php?cancel&amp;{TABLE}&amp;id={ITEM_ID}" title="Zrušiť úpravu popisu">
                                    <span><img src="img/cancel.png" alt=""></span>
                                </a>
                            {ELSEIF:{ITEM_EDIT}}
                                <span title="Druhý upravuje popis"><img src="img/edit_bad.png" alt=""></span>
                            {ELSE}
                                <a href="do/edit.php?{TABLE}&amp;id={ITEM_ID}" title="Upraviť popis"><span><img src="img/edit.png" alt=""></span></a>
                            {END_IF}
                        </td>
                    </tr>
                    {END_CYCLE}
                </tbody>
            </table>
        </form>
        <div class="pagination">
            <div class="first"><a href="{FIRST_SITE_LINK}" title="Prvá stránka">❮❮</a></div>
            <div class="prev"><a href="{PREV_SITE_LINK}" title="Predošlá stránka">❮</a></div>
            <div class="page">
                <form action="/" method="GET">
                    <input type="number" name="site" value="{ACT_SITE}" min="1" max="{MAX_SITE}" step="1" title="Aktuálna stránka"> / <span title="Počet stránok">{MAX_SITE}</span>
                    {IF:{OTHER_TABLE}}<input type="hidden" name="{TABLE}" value="">{ELSE}{END_IF}
                </form>
            </div>
            <div class="last"><a href="{LAST_SITE_LINK}" title="Posledná stránka">❯❯</a></div>
            <div class="next"><a href="{NEXT_SITE_LINK}" title="Ďalšia stránka">❯</a></div>
        </div>
        <div class="legend">
            <div>
                <div>
                    <span class="edit"><img src="img/edit.png" alt=""></span>
                    <span>Upraviť popis</span>
                </div>
                <div>
                    <span class="edit me"><img src="img/edit_ok.png" alt=""></span>
                    <span>Ja upravujem popis</span>
                </div>
                <div>
                    <span class="edit other"><img src="img/edit_bad.png" alt=""></span>
                    <span>Druhý upravuje popis</span>
                </div>
            </div>
            <div>
                <div>
                    <span class="edit ok"><img src="img/accept.png" alt=""></span>
                    <span>Potvrdiť úpravu popisu</span>
                </div>
                <div>
                    <span class="edit cancel"><img src="img/cancel.png" alt=""></span>
                    <span>Zrušiť úpravu popisu</span>
                </div>
            </div>
        </div>
    </div>
    <footer>
        <span>&copy; Bc. BALCAR Juraj</span>
    </footer>
</body>
</html>