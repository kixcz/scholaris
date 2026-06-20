import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/react';
import { BookMarked, Plus, Search, Filter, FileText, Tag } from 'lucide-react';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Badge } from '@/components/ui/badge';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'References', href: '/references' },
];

interface Reference {
    id: number;
    title: string;
    authors: string;
    year: number;
    type: string;
    status: string;
    tags: string[];
}

export default function References() {
    // Placeholder data - will be replaced with API data
    const references: Reference[] = [];

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="References" />
            <div className="flex flex-1 flex-col gap-4 p-4">
                {/* Header */}
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-3xl font-bold tracking-tight">References</h1>
                        <p className="text-muted-foreground">Manage your research papers and articles</p>
                    </div>
                    <Button>
                        <Plus className="mr-2 h-4 w-4" />
                        Add Reference
                    </Button>
                </div>

                {/* Filters */}
                <Card>
                    <CardHeader>
                        <CardTitle className="flex items-center gap-2">
                            <Filter className="h-5 w-5" />
                            Filters
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div className="flex gap-2">
                            <div className="flex-1">
                                <div className="relative">
                                    <Search className="absolute left-2 top-2.5 h-4 w-4 text-muted-foreground" />
                                    <Input placeholder="Search references..." className="pl-8" />
                                </div>
                            </div>
                            <Button variant="outline">
                                <Tag className="mr-2 h-4 w-4" />
                                Type
                            </Button>
                            <Button variant="outline">Status</Button>
                            <Button variant="outline">Tier</Button>
                        </div>
                    </CardContent>
                </Card>

                {/* References Table */}
                <Card>
                    <CardHeader>
                        <CardTitle>All References</CardTitle>
                        <CardDescription>{references.length} papers collected</CardDescription>
                    </CardHeader>
                    <CardContent>
                        {references.length === 0 ? (
                            <div className="flex flex-col items-center justify-center py-12 text-center">
                                <FileText className="h-16 w-16 text-muted-foreground mb-4" />
                                <h3 className="text-lg font-semibold">No references yet</h3>
                                <p className="text-sm text-muted-foreground mb-4">
                                    Start building your research library by adding papers
                                </p>
                                <Button>
                                    <Plus className="mr-2 h-4 w-4" />
                                    Add Your First Reference
                                </Button>
                            </div>
                        ) : (
                            <Table>
                                <TableHeader>
                                    <TableRow>
                                        <TableHead>Title</TableHead>
                                        <TableHead>Authors</TableHead>
                                        <TableHead>Year</TableHead>
                                        <TableHead>Type</TableHead>
                                        <TableHead>Status</TableHead>
                                        <TableHead>Tags</TableHead>
                                    </TableRow>
                                </TableHeader>
                                <TableBody>
                                    {references.map((ref) => (
                                        <TableRow key={ref.id}>
                                            <TableCell className="font-medium">{ref.title}</TableCell>
                                            <TableCell>{ref.authors}</TableCell>
                                            <TableCell>{ref.year}</TableCell>
                                            <TableCell>
                                                <Badge variant="outline">{ref.type}</Badge>
                                            </TableCell>
                                            <TableCell>
                                                <Badge
                                                    variant={
                                                        ref.status === 'completed'
                                                            ? 'default'
                                                            : ref.status === 'reading'
                                                              ? 'secondary'
                                                              : 'outline'
                                                    }
                                                >
                                                    {ref.status}
                                                </Badge>
                                            </TableCell>
                                            <TableCell>
                                                <div className="flex gap-1">
                                                    {ref.tags.map((tag) => (
                                                        <Badge key={tag} variant="secondary" className="text-xs">
                                                            {tag}
                                                        </Badge>
                                                    ))}
                                                </div>
                                            </TableCell>
                                        </TableRow>
                                    ))}
                                </TableBody>
                            </Table>
                        )}
                    </CardContent>
                </Card>
            </div>
        </AppLayout>
    );
}
