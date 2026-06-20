import AppLayout from '@/layouts/app-layout';
import { Head } from '@inertiajs/react';
import { Brain, Plus, BookOpen } from 'lucide-react';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { useState } from 'react';

const breadcrumbs = [{ title: 'Dashboard', href: '/dashboard' }, { title: 'Vocabulary', href: '/vocabulary' }];

export default function Vocabulary() {
    const [isOpen, setIsOpen] = useState(false);

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Vocabulary" />
            <div className="flex flex-1 flex-col gap-4 p-4">
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-3xl font-bold tracking-tight">Vocabulary</h1>
                        <p className="text-muted-foreground">Track technical terms and concepts</p>
                    </div>
                    <Button onClick={() => setIsOpen(true)}>
                        <Plus className="mr-2 h-4 w-4" />
                        Add Term
                    </Button>
                </div>

                <Dialog open={isOpen} onOpenChange={setIsOpen}>
                    <DialogContent className="max-w-2xl">
                        <DialogHeader>
                            <DialogTitle>Add New Term</DialogTitle>
                            <DialogDescription>
                                Store technical terms, definitions, and examples
                            </DialogDescription>
                        </DialogHeader>
                        <div className="grid gap-4 py-4">
                            <div className="grid gap-2">
                                <Label htmlFor="term">Term *</Label>
                                <Input id="term" placeholder="e.g., Convolutional Neural Network" />
                            </div>
                            <div className="grid gap-2">
                                <Label htmlFor="definition">Definition *</Label>
                                <Textarea id="definition" placeholder="Clear definition..." className="min-h-[80px]" />
                            </div>
                            <div className="grid gap-2">
                                <Label htmlFor="example">Example</Label>
                                <Textarea id="example" placeholder="Usage example or context..." />
                            </div>
                            <div className="grid grid-cols-2 gap-4">
                                <div className="grid gap-2">
                                    <Label htmlFor="category">Category</Label>
                                    <Select>
                                        <SelectTrigger>
                                            <SelectValue placeholder="Select category" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem value="ai">Artificial Intelligence</SelectItem>
                                            <SelectItem value="ml">Machine Learning</SelectItem>
                                            <SelectItem value="dl">Deep Learning</SelectItem>
                                            <SelectItem value="nlp">NLP</SelectItem>
                                            <SelectItem value="stats">Statistics</SelectItem>
                                            <SelectItem value="programming">Programming</SelectItem>
                                            <SelectItem value="research">Research Methods</SelectItem>
                                        </SelectContent>
                                    </Select>
                                </div>
                                <div className="grid gap-2">
                                    <Label htmlFor="importance">Importance</Label>
                                    <Select>
                                        <SelectTrigger>
                                            <SelectValue placeholder="Select importance" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem value="high">High</SelectItem>
                                            <SelectItem value="medium">Medium</SelectItem>
                                            <SelectItem value="low">Low</SelectItem>
                                        </SelectContent>
                                    </Select>
                                </div>
                            </div>
                            <div className="grid gap-2">
                                <Label htmlFor="mastery">Mastery Level</Label>
                                <Select>
                                    <SelectTrigger>
                                        <SelectValue placeholder="Current mastery" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="new">New</SelectItem>
                                        <SelectItem value="learning">Learning</SelectItem>
                                        <SelectItem value="familiar">Familiar</SelectItem>
                                        <SelectItem value="mastered">Mastered</SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>
                            <div className="grid gap-2">
                                <Label htmlFor="related_terms">Related Terms (comma-separated)</Label>
                                <Input id="related_terms" placeholder="neural network, deep learning, CNN" />
                            </div>
                        </div>
                        <DialogFooter>
                            <Button variant="outline" onClick={() => setIsOpen(false)}>
                                Cancel
                            </Button>
                            <Button onClick={() => setIsOpen(false)}>
                                Add Term
                            </Button>
                        </DialogFooter>
                    </DialogContent>
                </Dialog>

                <Card>
                    <CardHeader>
                        <CardTitle>All Terms</CardTitle>
                        <CardDescription>0 terms in your glossary</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div className="flex flex-col items-center justify-center py-12 text-center">
                            <BookOpen className="h-16 w-16 text-muted-foreground mb-4" />
                            <h3 className="text-lg font-semibold">No terms yet</h3>
                            <p className="text-sm text-muted-foreground mb-4">
                                Build your technical vocabulary
                            </p>
                            <Button onClick={() => setIsOpen(true)}>
                                <Plus className="mr-2 h-4 w-4" />
                                Add Your First Term
                            </Button>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </AppLayout>
    );
}
