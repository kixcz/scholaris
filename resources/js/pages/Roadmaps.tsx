import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/react';
import { Route, Plus, Map } from 'lucide-react';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Roadmaps', href: '/roadmaps' },
];

export default function Roadmaps() {
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Roadmaps" />
            <div className="flex flex-1 flex-col gap-4 p-4">
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-3xl font-bold tracking-tight">Roadmaps</h1>
                        <p className="text-muted-foreground">Plan your long-term research journey</p>
                    </div>
                    <Button>
                        <Plus className="mr-2 h-4 w-4" />
                        Create Roadmap
                    </Button>
                </div>

                <Card>
                    <CardHeader>
                        <CardTitle>Your Roadmaps</CardTitle>
                        <CardDescription>0 active roadmaps</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div className="flex flex-col items-center justify-center py-12 text-center">
                            <Map className="h-16 w-16 text-muted-foreground mb-4" />
                            <h3 className="text-lg font-semibold">No roadmaps yet</h3>
                            <p className="text-sm text-muted-foreground mb-4">
                                Create roadmaps to plan your research milestones
                            </p>
                            <Button>
                                <Plus className="mr-2 h-4 w-4" />
                                Create Your First Roadmap
                            </Button>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </AppLayout>
    );
}
