import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/react';
import { BookMarked, Notebook, Brain, Target, Route, Link2, BarChart3, TrendingUp } from 'lucide-react';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];

interface StatCard {
    title: string;
    value: string | number;
    description: string;
    icon: any;
    href: string;
    color: string;
}

export default function Dashboard() {
    const stats: StatCard[] = [
        {
            title: 'References',
            value: '0',
            description: 'Papers & articles collected',
            icon: BookMarked,
            href: '/references',
            color: 'bg-blue-500',
        },
        {
            title: 'Notebooks',
            value: '0',
            description: 'Research notes organized',
            icon: Notebook,
            href: '/notebooks',
            color: 'bg-purple-500',
        },
        {
            title: 'Vocabulary',
            value: '0',
            description: 'Terms mastered',
            icon: Brain,
            href: '/vocabulary',
            color: 'bg-green-500',
        },
        {
            title: 'Weekly Goals',
            value: '0',
            description: 'Goals completed this week',
            icon: Target,
            href: '/weekly-goals',
            color: 'bg-orange-500',
        },
        {
            title: 'Roadmaps',
            value: '0',
            description: 'Active research plans',
            icon: Route,
            href: '/roadmaps',
            color: 'bg-indigo-500',
        },
        {
            title: 'Links',
            value: '0',
            description: 'Resources bookmarked',
            icon: Link2,
            href: '/links',
            color: 'bg-pink-500',
        },
    ];

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Dashboard" />
            <div className="flex flex-1 flex-col gap-4 p-4">
                <div className="rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 p-6 text-white">
                    <h1 className="text-3xl font-bold">Welcome to Scholaris</h1>
                    <p className="mt-2 text-blue-100">Your Scholarly Operating System for research excellence</p>
                </div>

                <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                    {stats.map((stat) => (
                        <Card key={stat.title} className="hover:shadow-lg transition-shadow">
                            <Link href={stat.href}>
                                <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                                    <CardTitle className="text-sm font-medium">{stat.title}</CardTitle>
                                    <div className={`${stat.color} rounded-lg p-2`}>
                                        <stat.icon className="h-4 w-4 text-white" />
                                    </div>
                                </CardHeader>
                                <CardContent>
                                    <div className="text-2xl font-bold">{stat.value}</div>
                                    <p className="text-xs text-muted-foreground">{stat.description}</p>
                                </CardContent>
                            </Link>
                        </Card>
                    ))}
                </div>

                <Card>
                    <CardHeader>
                        <CardTitle>Quick Actions</CardTitle>
                        <CardDescription>Start your scholarly work</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div className="grid gap-3 md:grid-cols-2 lg:grid-cols-4">
                            <Button asChild variant="outline">
                                <Link href="/references">
                                    <BookMarked className="mr-2 h-4 w-4" />
                                    Add Reference
                                </Link>
                            </Button>
                            <Button asChild variant="outline">
                                <Link href="/notebooks">
                                    <Notebook className="mr-2 h-4 w-4" />
                                    Create Notebook
                                </Link>
                            </Button>
                            <Button asChild variant="outline">
                                <Link href="/vocabulary">
                                    <Brain className="mr-2 h-4 w-4" />
                                    Add Term
                                </Link>
                            </Button>
                            <Button asChild variant="outline">
                                <Link href="/weekly-goals">
                                    <Target className="mr-2 h-4 w-4" />
                                    Set Weekly Goal
                                </Link>
                            </Button>
                        </div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle className="flex items-center gap-2">
                            <TrendingUp className="h-5 w-5" />
                            Productivity Overview
                        </CardTitle>
                        <CardDescription>Your scholarly activity at a glance</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <Link href="/productivity">
                            <Button variant="outline" className="w-full">
                                <BarChart3 className="mr-2 h-4 w-4" />
                                View Full Productivity Dashboard
                            </Button>
                        </Link>
                    </CardContent>
                </Card>
            </div>
        </AppLayout>
    );
}
