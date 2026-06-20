import AppLayout from '@/layouts/app-layout';
import { Head } from '@inertiajs/react';
import { BookMarked, Plus, FileText, ExternalLink } from 'lucide-react';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Badge } from '@/components/ui/badge';
import { useState } from 'react';

const breadcrumbs = [{ title: 'Dashboard', href: '/dashboard' }, { title: 'References', href: '/references' }];

export default function References() {
    const [isOpen, setIsOpen] = useState(false);

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="References" />
            <div className="flex flex-1 flex-col gap-4 p-4">
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-3xl font-bold tracking-tight">References</h1>
                        <p className="text-muted-foreground">Manage your research papers and articles</p>
                    </div>
                    <Button onClick={() => setIsOpen(true)}>
                        <Plus className="mr-2 h-4 w-4" />
                        Add Reference
                    </Button>
                </div>

                <Dialog open={isOpen} onOpenChange={setIsOpen}>
                    <DialogContent className="max-w-3xl max-h-[90vh] overflow-y-auto">
                        <DialogHeader>
                            <DialogTitle>Add New Reference</DialogTitle>
                            <DialogDescription>
                                Add journal articles, books, conference papers, or reports
                            </DialogDescription>
                        </DialogHeader>
                        <div className="grid gap-4 py-4">
                            <div className="grid gap-2">
                                <Label htmlFor="title">Title *</Label>
                                <Input id="title" placeholder="Paper title..." />
                            </div>
                            <div className="grid gap-2">
                                <Label htmlFor="authors">Authors *</Label>
                                <Input id="authors" placeholder="e.g., Smith, J., Johnson, A." />
                            </div>
                            <div className="grid grid-cols-3 gap-4">
                                <div className="grid gap-2">
                                    <Label htmlFor="year">Publication Year</Label>
                                    <Input id="year" type="number" placeholder="2024" />
                                </div>
                                <div className="grid gap-2">
                                    <Label htmlFor="type">Type</Label>
                                    <Select>
                                        <SelectTrigger>
                                            <SelectValue placeholder="Select type" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem value="journal">Journal Article</SelectItem>
                                            <SelectItem value="conference">Conference Paper</SelectItem>
                                            <SelectItem value="book">Book</SelectItem>
                                            <SelectItem value="report">Technical Report</SelectItem>
                                            <SelectItem value="website">Website</SelectItem>
                                            <SelectItem value="thesis">Thesis</SelectItem>
                                        </SelectContent>
                                    </Select>
                                </div>
                                <div className="grid gap-2">
                                    <Label htmlFor="status">Status</Label>
                                    <Select>
                                        <SelectTrigger>
                                            <SelectValue placeholder="Select status" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem value="collected">Collected</SelectItem>
                                            <SelectItem value="reading">Reading</SelectItem>
                                            <SelectItem value="completed">Completed</SelectItem>
                                            <SelectItem value="archived">Archived</SelectItem>
                                        </SelectContent>
                                    </Select>
                                </div>
                            </div>
                            <div className="grid grid-cols-2 gap-4">
                                <div className="grid gap-2">
                                    <Label htmlFor="doi">DOI</Label>
                                    <Input id="doi" placeholder="10.xxxx/xxxxx" />
                                </div>
                                <div className="grid gap-2">
                                    <Label htmlFor="url">URL</Label>
                                    <Input id="url" type="url" placeholder="https://..." />
                                </div>
                            </div>
                            <div className="grid gap-2">
                                <Label htmlFor="abstract">Abstract</Label>
                                <Textarea id="abstract" placeholder="Paper abstract..." className="min-h-[100px]" />
                            </div>
                            <div className="grid gap-2">
                                <Label htmlFor="keywords">Keywords (comma-separated)</Label>
                                <Input id="keywords" placeholder="machine learning, AI, deep learning" />
                            </div>
                            <div className="rounded-lg border p-4">
                                <Label className="mb-3 block">Categorization</Label>
                                <div className="grid grid-cols-2 gap-4">
                                    <div className="grid gap-2">
                                        <Label htmlFor="tier">Tier</Label>
                                        <Select>
                                            <SelectTrigger>
                                                <SelectValue placeholder="Select tier" />
                                            </SelectTrigger>
                                            <SelectContent>
                                                <SelectItem value="core">Core</SelectItem>
                                                <SelectItem value="important">Important</SelectItem>
                                                <SelectItem value="supplementary">Supplementary</SelectItem>
                                            </SelectContent>
                                        </Select>
                                    </div>
                                    <div className="grid gap-2">
                                        <Label htmlFor="pillar">Pillar</Label>
                                        <Select>
                                            <SelectTrigger>
                                                <SelectValue placeholder="Select pillar" />
                                            </SelectTrigger>
                                            <SelectContent>
                                                <SelectItem value="methodology">Methodology</SelectItem>
                                                <SelectItem value="theory">Theory</SelectItem>
                                                <SelectItem value="empirical">Empirical</SelectItem>
                                                <SelectItem value="review">Review</SelectItem>
                                            </SelectContent>
                                        </Select>
                                    </div>
                                </div>
                            </div>
                            <div className="grid gap-2">
                                <Label htmlFor="linked_roadmap">Linked Roadmap (Optional)</Label>
                                <Select>
                                    <SelectTrigger>
                                        <SelectValue placeholder="Select roadmap" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="1">Publish Q2 Journal Paper</SelectItem>
                                        <SelectItem value="2">PhD Thesis Completion</SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>
                        </div>
                        <DialogFooter>
                            <Button variant="outline" onClick={() => setIsOpen(false)}>
                                Cancel
                            </Button>
                            <Button onClick={() => setIsOpen(false)}>
                                Add Reference
                            </Button>
                        </DialogFooter>
                    </DialogContent>
                </Dialog>

                <Card>
                    <CardHeader>
                        <CardTitle>All References</CardTitle>
                        <CardDescription>0 papers collected</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div className="flex flex-col items-center justify-center py-12 text-center">
                            <FileText className="h-16 w-16 text-muted-foreground mb-4" />
                            <h3 className="text-lg font-semibold">No references yet</h3>
                            <p className="text-sm text-muted-foreground mb-4">
                                Start building your research library
                            </p>
                            <Button onClick={() => setIsOpen(true)}>
                                <Plus className="mr-2 h-4 w-4" />
                                Add Your First Reference
                            </Button>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </AppLayout>
    );
}
