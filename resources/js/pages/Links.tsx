import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/react';
import { Link2, Plus, Bookmark } from 'lucide-react';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Links', href: '/links' },
];

export default function Links() {
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Links" />
            <div className="flex flex-1 flex-col gap-4 p-4">
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-3xl font-bold tracking-tight">Links</h1>
                        <p className="text-muted-foreground">Bookmark useful resources and tools</p>
                    </div>
                    <Button>
                        <Plus className="mr-2 h-4 w-4" />
                        Add Link
                    </Button>
                </div>

                <Card>
                    <CardHeader>
                        <CardTitle>Bookmarked Links</CardTitle>
                        <CardDescription>0 links saved</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div className="flex flex-col items-center justify-center py-12 text-center">
                            <Bookmark className="h-16 w-16 text-muted-foreground mb-4" />
                            <h3 className="text-lg font-semibold">No links yet</h3>
                            <p className="text-sm text-muted-foreground mb-4">
                                Bookmark useful resources, tools, and references
                            </p>
                            <Button>
                                <Plus className="mr-2 h-4 w-4" />
                                Add Your First Link
                            </Button>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </AppLayout>
    );
}
