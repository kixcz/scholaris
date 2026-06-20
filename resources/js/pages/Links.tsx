import AppLayout from '@/layouts/app-layout';
import { Head } from '@inertiajs/react';
import { Link2, Plus, Bookmark } from 'lucide-react';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { useState } from 'react';

const breadcrumbs = [{ title: 'Dashboard', href: '/dashboard' }, { title: 'Links', href: '/links' }];

export default function Links() {
    const [isOpen, setIsOpen] = useState(false);

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Links" />
            <div className="flex flex-1 flex-col gap-4 p-4">
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-3xl font-bold tracking-tight">Links</h1>
                        <p className="text-muted-foreground">Bookmark useful resources and tools</p>
                    </div>
                    <Button onClick={() => setIsOpen(true)}>
                        <Plus className="mr-2 h-4 w-4" />
                        Add Link
                    </Button>
                </div>

                <Dialog open={isOpen} onOpenChange={setIsOpen}>
                    <DialogContent className="max-w-2xl">
                        <DialogHeader>
                            <DialogTitle>Add New Link</DialogTitle>
                            <DialogDescription>
                                Save tutorial videos, courses, documentation, or resources
                            </DialogDescription>
                        </DialogHeader>
                        <div className="grid gap-4 py-4">
                            <div className="grid gap-2">
                                <Label htmlFor="title">Title *</Label>
                                <Input id="title" placeholder="e.g., Complete Python Tutorial" />
                            </div>
                            <div className="grid gap-2">
                                <Label htmlFor="url">URL *</Label>
                                <Input id="url" type="url" placeholder="https://..." />
                            </div>
                            <div className="grid gap-2">
                                <Label htmlFor="description">Description</Label>
                                <Textarea id="description" placeholder="What is this resource about?" />
                            </div>
                            <div className="grid grid-cols-2 gap-4">
                                <div className="grid gap-2">
                                    <Label htmlFor="category">Category</Label>
                                    <Select>
                                        <SelectTrigger>
                                            <SelectValue placeholder="Select category" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem value="tutorial">Tutorial</SelectItem>
                                            <SelectItem value="course">Course</SelectItem>
                                            <SelectItem value="documentation">Documentation</SelectItem>
                                            <SelectItem value="video">Video</SelectItem>
                                            <SelectItem value="article">Article</SelectItem>
                                            <SelectItem value="github">GitHub Repository</SelectItem>
                                            <SelectItem value="dataset">Dataset</SelectItem>
                                            <SelectItem value="tool">Tool/Software</SelectItem>
                                        </SelectContent>
                                    </Select>
                                </div>
                                <div className="grid gap-2">
                                    <Label htmlFor="difficulty">Difficulty Level</Label>
                                    <Select>
                                        <SelectTrigger>
                                            <SelectValue placeholder="Select difficulty" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem value="beginner">Beginner</SelectItem>
                                            <SelectItem value="intermediate">Intermediate</SelectItem>
                                            <SelectItem value="advanced">Advanced</SelectItem>
                                        </SelectContent>
                                    </Select>
                                </div>
                            </div>
                            <div className="grid gap-2">
                                <Label htmlFor="estimated_time">Estimated Completion Time</Label>
                                <Input id="estimated_time" placeholder="e.g., 2 hours, 3 weeks" />
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
                                Add Link
                            </Button>
                        </DialogFooter>
                    </DialogContent>
                </Dialog>

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
                            <Button onClick={() => setIsOpen(true)}>
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
