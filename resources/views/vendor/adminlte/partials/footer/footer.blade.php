<footer class="main-footer">
    @hasSection('footer')
        @yield('footer')
    @else
        <div class="text-right bg-primary text-white py-1">
            <strong>© 2025 <a href="#" class="text-white">Sistema</a>. Todos os direitos reservados.</strong>
        </div>
    @endif
</footer>