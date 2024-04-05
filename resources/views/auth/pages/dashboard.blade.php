<x-auth-layout page-title="Dashboard">
    @hasrole('admin')
        Admin
    @endhasrole

    @hasrole('user')
        User Dashboard
    @endhasrole

    @hasrole('dealer')
        Dealer Dashboard
    @endhasrole
</x-auth-layout>
