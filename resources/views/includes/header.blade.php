

<nav class="HeaderNav">
    <label class="name">Spornt</label>
    <ul>
        <li><a class="logout" href="#" style="@if(app()->getLocale() != 'en') {{ 'font-size: 13px; padding: 5px' }} @endif">{{ __('messages.Logout') }}</a></li>
    </ul>
    <ul class="ChangeLang">
            <div>
                @foreach (Config::get('languages') as $lang => $language)
                    @if ($lang != App::getLocale())
                        <a class="language-switch" href="{{ route('lang.switch', $lang) }}">{{$language}}</a>
                    @endif
                @endforeach
            </div>
        </li>
    </ul>
</nav>
