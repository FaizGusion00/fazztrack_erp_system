<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ClientController extends Controller
{
    /**
     * Display a listing of clients
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        
        // Check role-based access
        if (!$user->isSuperAdmin() && !$user->isAdmin() && !$user->isSalesManager()) {
            abort(403, 'Access denied. You do not have permission to view clients.');
        }
        
        $query = Client::with('contacts');
        
        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }
        
        // Filter by customer type
        if ($request->filled('customer_type')) {
            $query->where('customer_type', $request->customer_type);
        }
        
        $clients = $query->paginate(15)->withQueryString();
        return view('clients.index', compact('clients'));
    }

    /**
     * Show the form for creating a new client
     */
    public function create()
    {
        $user = auth()->user();
        
        // Check role-based access
        if (!$user->isSuperAdmin() && !$user->isAdmin() && !$user->isSalesManager()) {
            abort(403, 'Access denied. You do not have permission to create clients.');
        }
        
        return view('clients.create');
    }

    /**
     * Store a newly created client
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        
        // Check role-based access
        if (!$user->isSuperAdmin() && !$user->isAdmin() && !$user->isSalesManager()) {
            abort(403, 'Access denied. You do not have permission to create clients.');
        }
        
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|unique:clients,email',
            'billing_address' => 'required|string',
            'shipping_address' => 'nullable|string',
            'customer_type' => 'required|in:Individual,Agent,Organisation',
            'contacts' => 'nullable|array',
            'contacts.*.contact_name' => 'required|string|max:255',
            'contacts.*.contact_phone' => 'required|string|max:20',
            'contacts.*.contact_email' => 'required|email',
        ]);

        $client = Client::create($request->only([
            'name', 'phone', 'email', 'billing_address', 
            'shipping_address', 'customer_type'
        ]));

        // Create contacts if provided
        if ($request->has('contacts')) {
            foreach ($request->contacts as $contactData) {
                $client->contacts()->create($contactData);
            }
        }

        return redirect()->route('clients.index')
            ->with('success', 'Client created successfully.');
    }

    /**
     * Display the specified client
     */
    public function show(Client $client)
    {
        $client->load(['contacts', 'orders.jobs']);
        return view('clients.show', compact('client'));
    }

    /**
     * Show the form for editing the specified client
     */
    public function edit(Client $client)
    {
        return view('clients.edit', compact('client'));
    }

    /**
     * Update the specified client
     */
    public function update(Request $request, Client $client)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|unique:clients,email,' . $client->client_id . ',client_id',
            'billing_address' => 'required|string',
            'shipping_address' => 'nullable|string',
            'customer_type' => 'required|in:Individual,Agent,Organisation',
        ]);

        $client->update($request->only([
            'name', 'phone', 'email', 'billing_address', 
            'shipping_address', 'customer_type'
        ]));

        return redirect()->route('clients.index')
            ->with('success', 'Client updated successfully.');
    }

    /**
     * Remove the specified client
     */
    public function destroy(Client $client)
    {
        $user = auth()->user();
        
        // Only SuperAdmin can delete clients
        if (!$user->isSuperAdmin()) {
            abort(403, 'Access denied. Only SuperAdmin can delete clients.');
        }
        
        $client->delete();
        return redirect()->route('clients.index')
            ->with('success', 'Client deleted successfully.');
    }

    /**
     * Add contact to client
     */
    public function addContact(Request $request, Client $client)
    {
        $request->validate([
            'contact_name' => 'required|string|max:255',
            'contact_phone' => 'required|string|max:20',
            'contact_email' => 'required|email',
        ]);

        $client->contacts()->create($request->only([
            'contact_name', 'contact_phone', 'contact_email'
        ]));

        return redirect()->route('clients.show', $client)
            ->with('success', 'Contact added successfully.');
    }

    /**
     * Remove contact from client
     */
    public function removeContact(Client $client, Contact $contact)
    {
        $contact->delete();
        return redirect()->route('clients.show', $client)
            ->with('success', 'Contact removed successfully.');
    }
} 