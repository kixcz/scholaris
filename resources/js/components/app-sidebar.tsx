import { NavFooter } from '@/components/nav-footer';
import { NavMain } from '@/components/nav-main';
import { NavUser } from '@/components/nav-user';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/react';
import { BookOpen, Folder, LayoutGrid, BookMarked, Notebook, Brain, Target, Route, Link2, BarChart3, Pill } from 'lucide-react';
import AppLogo from './app-logo';

const mainNavItems: NavItem[] = [
    
    {
        title: 'Dashboard',
        url: '/dashboard',
        icon: LayoutGrid,
    },
    {
        title: 'Pillars',
        url: '/pillars',
        icon: Pill,
    },
    {
        title: 'References',
        url: '/references',
        icon: BookMarked,
    },
    {
        title: 'Notebooks',
        url: '/notebooks',
        icon: Notebook,
    },
    {
        title: 'Vocabulary',
        url: '/vocabulary',
        icon: Brain,
    },
    {
        title: 'Weekly Goals',
        url: '/weekly-goals',
        icon: Target,
    },
    {
        title: 'Roadmaps',
        url: '/roadmaps',
        icon: Route,
    },
    {
        title: 'Links',
        url: '/links',
        icon: Link2,
    },
    {
        title: 'Productivity',
        url: '/productivity',
        icon: BarChart3,
    },
];

const footerNavItems: NavItem[] = [
    {
        title: 'Scholaris OS',
        url: 'https://github.com/scholaris',
        icon: Folder,
    },
    {
        title: 'Documentation',
        url: 'https://docs.scholaris.app',
        icon: BookOpen,
    },
];

export function AppSidebar() {
    return (
        <Sidebar collapsible="icon" variant="inset">
            <SidebarHeader>
                <SidebarMenu>
                    <SidebarMenuItem>
                        <SidebarMenuButton size="lg" asChild>
                            <Link href="/dashboard" prefetch>
                                <AppLogo />
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
            </SidebarHeader>

            <SidebarContent>
                <NavMain items={mainNavItems} />
            </SidebarContent>

            <SidebarFooter>
                <NavFooter items={footerNavItems} className="mt-auto" />
                <NavUser />
            </SidebarFooter>
        </Sidebar>
    );
}
