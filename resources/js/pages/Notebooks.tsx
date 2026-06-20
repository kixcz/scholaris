import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/react';
import { Notebook, Plus, BookOpen } from 'lucide-react';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Notebooks', href: '/notebooks' },
];

export default function Notebooks() {
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Notebooks" />
            <div className="flex flex-1 flex-col gap-4 p-4">
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-3xl font-bold tracking-tight">Notebooks</h1>
                        <p className="text-muted-foreground">Organize your research notes</p>
                    </div>
                    <Button>
                        <Plus className="mr-2 h-4 w-4" />
                        Create Notebook
                    </Button>
                </div>

                <Card>
                    <CardHeader>
                        <CardTitle>Your Notebooks</CardTitle>
                        <CardDescription>0 notebooks created</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div className="flex flex-col items-center justify-center py-12 text-center">
                            <BookOpen className="h-16 w-16 text-muted-foreground mb-4" />
                            <h3 className="text-lg font-semibold">No notebooks yet</h3>
                            <p className="text-sm text-muted-foreground mb-4">
                                Create notebooks to organize your research notes
                            </p>
                            <Button>
                                <Plus className="mr-2 h-4 w-4" />
                                Create Your First Notebook
                            </Button>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </AppLayout>
    );
}
