import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/react';
import { Brain, Plus, Search, BookOpen } from 'lucide-react';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Badge } from '@/components/ui/badge';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Vocabulary', href: '/vocabulary' },
];

interface VocabularyTerm {
    id: number;
    term: string;
    definition: string;
    category: string;
    importance: string;
    mastered: boolean;
}

export default function Vocabulary() {
    const terms: VocabularyTerm[] = [];

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Vocabulary" />
            <div className="flex flex-1 flex-col gap-4 p-4">
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-3xl font-bold tracking-tight">Vocabulary</h1>
                        <p className="text-muted-foreground">Track technical terms and concepts</p>
                    </div>
                    <Button>
                        <Plus className="mr-2 h-4 w-4" />
                        Add Term
                    </Button>
                </div>

                <Card>
                    <CardHeader>
                        <div className="relative">
                            <Search className="absolute left-2 top-2.5 h-4 w-4 text-muted-foreground" />
                            <Input placeholder="Search terms..." className="pl-8" />
                        </div>
                    </CardHeader>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle>All Terms</CardTitle>
                        <CardDescription>{terms.length} terms in your glossary</CardDescription>
                    </CardHeader>
                    <CardContent>
                        {terms.length === 0 ? (
                            <div className="flex flex-col items-center justify-center py-12 text-center">
                                <BookOpen className="h-16 w-16 text-muted-foreground mb-4" />
                                <h3 className="text-lg font-semibold">No terms yet</h3>
                                <p className="text-sm text-muted-foreground mb-4">
                                    Build your technical vocabulary by adding terms
                                </p>
                                <Button>
                                    <Plus className="mr-2 h-4 w-4" />
                                    Add Your First Term
                                </Button>
                            </div>
                        ) : (
                            <Table>
                                <TableHeader>
                                    <TableRow>
                                        <TableHead>Term</TableHead>
                                        <TableHead>Definition</TableHead>
                                        <TableHead>Category</TableHead>
                                        <TableHead>Importance</TableHead>
                                        <TableHead>Status</TableHead>
                                    </TableRow>
                                </TableHeader>
                                <TableBody>
                                    {terms.map((term) => (
                                        <TableRow key={term.id}>
                                            <TableCell className="font-medium">{term.term}</TableCell>
                                            <TableCell className="max-w-md truncate">{term.definition}</TableCell>
                                            <TableCell>
                                                <Badge variant="outline">{term.category}</Badge>
                                            </TableCell>
                                            <TableCell>
                                                <Badge
                                                    variant={
                                                        term.importance === 'high'
                                                            ? 'destructive'
                                                            : term.importance === 'medium'
                                                              ? 'default'
                                                              : 'secondary'
                                                    }
                                                >
                                                    {term.importance}
                                                </Badge>
                                            </TableCell>
                                            <TableCell>
                                                {term.mastered ? (
                                                    <Badge variant="default">Mastered</Badge>
                                                ) : (
                                                    <Badge variant="outline">Learning</Badge>
                                                )}
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
