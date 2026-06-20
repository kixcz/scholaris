import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/react';
import { Target, Plus, CheckCircle2 } from 'lucide-react';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Weekly Goals', href: '/weekly-goals' },
];

export default function WeeklyGoals() {
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Weekly Goals" />
            <div className="flex flex-1 flex-col gap-4 p-4">
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-3xl font-bold tracking-tight">Weekly Goals</h1>
                        <p className="text-muted-foreground">Track your weekly research targets</p>
                    </div>
                    <Button>
                        <Plus className="mr-2 h-4 w-4" />
                        Create Goal
                    </Button>
                </div>

                <Card>
                    <CardHeader>
                        <CardTitle>This Week's Goals</CardTitle>
                        <CardDescription>0 goals set for this week</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div className="flex flex-col items-center justify-center py-12 text-center">
                            <CheckCircle2 className="h-16 w-16 text-muted-foreground mb-4" />
                            <h3 className="text-lg font-semibold">No goals yet</h3>
                            <p className="text-sm text-muted-foreground mb-4">
                                Set weekly goals to track your research progress
                            </p>
                            <Button>
                                <Plus className="mr-2 h-4 w-4" />
                                Set Your First Weekly Goal
                            </Button>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </AppLayout>
    );
}
